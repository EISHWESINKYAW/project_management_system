<?php

namespace App\Repositories;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Events\NotifyUser;
use App\Events\TaskCreated;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Events\ProjectStatusChange;
use Illuminate\Support\Facades\Auth;
use App\Events\ProjectTaskStatusChange;


class TaskRepository
{
    protected function model()
    {
        return Task::query();
    }

    public function list(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|numeric',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'creator' => 'nullable|string',
        ]);

        $authUser = Auth::user();

        $name = $request->input('name');
        $description = $request->input('description');
        $creator = $request->input('creator');
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');

        $tasks = $this->model()
            ->isCreatorOrCollaborator($authUser->id)
            ->with(['creator', 'project', 'collaborators'])
            ->when($name, function ($query) use ($name) {
                $query->where(function ($q) use ($name) {
                    $q->where('name', 'LIKE', '%' . $name . '%')
                        ->orWhere('description', 'LIKE', '%' . $name . '%');
                });
            })
            ->when($description, function ($query) use ($description) {
                $query->where(function ($q) use ($description) {
                    $q->where('description', 'LIKE', '%' . $description . '%')
                        ->orWhere('name', 'LIKE', '%' . $description . '%');
                });
            })
            ->when($creator, function ($query) use ($creator) {
                $query->whereHas('creator', function ($q) use ($creator) {
                    $q->where('name', 'LIKE', '%' . $creator . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($perPage);

        return $tasks;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'due_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            $task = $this->model()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? Task::STATUS_PENDING,
                'due_date' => $validated['due_date'] ?? null,
                'project_id' => $validated['project_id'],
                'created_by' => Auth::id(),
            ]);

            $this->setInProgressState($validated['project_id']);

            if (!empty($validated['collaborators'])) {
                $task->collaborators()->attach($validated['collaborators']);

                collect($validated['collaborators'])->each(function ($userId) use ($task) {
                    $user = User::find($userId);
                    if ($user) {
                        $notification = Notification::recordNotification(
                            $task,
                            'New Task Assigned',
                            'You have been assigned to a new task: ' . $task->name,
                            $userId,
                            'task/detail'
                        );

                        NotifyUser::dispatch($user, $notification);
                    }
                });
            }

            broadcast(new TaskCreated($task));

            DB::commit();

            return $task->load(['collaborators', 'project']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create task: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $task = $this->model()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,completed,in_progress',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        abort_unless($task->isCreator($authUser->id), 403, 'You are not authorized to update this task.');

        DB::beginTransaction();

        try {
            $task->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'pending',
                'due_date' => $validated['due_date'] ?? null,
                'project_id' => $validated['project_id'],
            ]);



            if (isset($validated['collaborators'])) {

                $removedColaborators = $task->collaborators()->whereNotIn('users.id', $validated['collaborators'])->get();
                if (count($removedColaborators) > 0) {
                    $removedColaborators->map(function ($colab) use ($task) {
                        $notification = Notification::recordNotification(
                            $task,
                            'Collaborator removal!',
                            'You have been removed from the ' . $task->name . 'task.',
                            $colab->id,
                            'project'
                        );

                        NotifyUser::dispatch($colab, $notification);
                    });
                }

                $task->collaborators()->sync($validated['collaborators']);
            }

            DB::commit();

            return $task->load(['collaborators', 'project']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update task: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $task = $this->model()->with(['creator', 'project', 'collaborators'])->findOrFail($id);

        return $task;
    }

    public function updateStatus(Request $request, $id)
    {
        $authUser = Auth::user();
        $task = $this->model()->findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(Task::STATUSES),
            ],
        ]);

        abort_unless($task->isCreatorOrCollaborator($authUser->id), 403, 'You are not authorized to update this task status.');

        DB::beginTransaction();

        try {
            $task->update(['status' => $validated['status']]);

            if ($validated['status'] === Task::STATUS_IN_PROGRESS) {
                $this->setInProgressState($task->project_id);
            }

            ProjectTaskStatusChange::dispatch($task, $task->project_id);

            DB::commit();

            return [
                'message' => 'Task status updated successfully',
                'status' => 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Project task status change fail: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $task = $this->model()->findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    protected function setInProgressState($projectId): void
    {
        $project = Project::find($projectId);
        $authUser = Auth::user();

        abort_unless($project, 404, 'Project not found');

        $projectTasks = $project->tasks()->count();

        if ($projectTasks == 1) {
            abort_unless($project->isCreator($authUser->id), 403, 'You are not authorized to update this project.');
            $project->update(['status' => Project::STATUS_IN_PROGRESS]);
        }

        ProjectStatusChange::dispatch($project);
    }
}
