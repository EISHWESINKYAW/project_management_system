<?php

namespace App\Repositories;

use App\Events\NotifyUser;
use App\Events\ProjectStatusChange;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepository
{
    protected function model()
    {
        return Project::query();
    }

    public function list(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|numeric',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'creator' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $authUser = Auth::user();

        $name = $request->input('name');
        $description = $request->input('description');
        $creator = $request->input('creator');
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');


        $projects = $this->model()
            ->when(!isSuperAdmin($authUser), function ($query) use ($authUser) {
                $query->isCreatorOrCollaborator($authUser->id);
            })
            ->with(['creator', 'collaborators'])
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

        return $projects;
    }

    public function listForTaskStore()
    {
        return $this->model()->latest()->get();
    }

    public function colaborator()
    {
        return User::query()->withWhereHas('role', function ($query) {
            $query->whereNotIn('slug', Role::MANAGEMENT_ROLE);
        })->get();
    }

    public function projectColaborator($id)
    {
        $project = $this->model()->findOrFail($id);

        return $project->collaborators;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'collaborators' => 'nullable|array', // array of user_ids
            'collaborators.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            $project = $this->model()->create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(6), // unique slug
                'description' => $request->description,
                'created_by' => Auth::id(),
                'due_date' => $request->due_date,
            ]);

            // Attach collaborators if provided
            if ($request->has('collaborators')) {
                $project->collaborators()->attach($request->collaborators);

                collect($request->collaborators)->each(function ($userId) use ($project) {
                    $user = User::find($userId);
                    if ($user) {
                        $notification = Notification::recordNotification(
                            $project,
                            'New Project Assigned',
                            'You have been assigned to a new project: ' . $project->name,
                            $userId,
                            'project'
                        );

                        NotifyUser::dispatch($user, $notification);
                    }
                });
            }

            DB::commit();

            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to create project: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        return $this->model()->with(['creator', 'collaborators'])->findOrFail($id);
    }

    public function detailWithTasks($id)
    {
        $authUser = Auth::user();

        $detail = $this->model()
            ->with([
                'creator',
                'collaborators',
                'tasks' => function ($query) use ($authUser) {
                    $query->isCreatorOrCollaborator($authUser->id);
                }
            ])
            ->findOrFail($id);

        return $detail;
    }

    public function update(Request $request, $id)
    {
        $project = $this->model()->findOrFail($id);
        $authUser = Auth::user();

        abort_unless($project->isCreator($authUser->id), 403, 'You are not authorized to update this project.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Update collaborators if provided
            if ($request->has('collaborators')) {
                $removedColaborators = $project->collaborators()->whereNotIn('users.id', $request->input('collaborators'))->get();
                if (count($removedColaborators) > 0) {
                    $removedColaborators->map(function ($colab) use ($project) {
                        $notification = Notification::recordNotification(
                            $project,
                            'Collaborator removal!',
                            'You have been removed from the ' . $project->name . 'project.',
                            $colab->id,
                            'project'
                        );

                        NotifyUser::dispatch($colab, $notification);
                    });
                }

                $project->collaborators()->sync($request->collaborators); // replaces old with new
            }

            DB::commit();

            return $project->load('collaborators'); // eager-load collaborators

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Project update fail: ' . $e->getMessage());
        }
    }

    public function makeComplete($id)
    {
        $project = $this->model()->find($id);
        $authUser = Auth::user();

        abort_unless($project, 404, 'Project not found.');

        abort_unless($project->isCreatorOrCollaborator($authUser->id), 403, 'You are not authorized to complete this project.');

        if ($project->status === Project::STATUS_COMPLETED) {
            return response()->json([
                'message' => 'Project is already completed.',
                'status' => 400,
            ]);
        }

        $incompleteTasks = $project->tasks()->where('status', '!=', Task::STATUS_COMPLETED)->count();

        abort_if($incompleteTasks > 0, 400, 'Cannot mark project as complete while there are incomplete tasks.');

        $project->update([
            'status' => Project::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        ProjectStatusChange::dispatch($project);

        return $project;
    }

    public function delete($id)
    {
        $authUser = Auth::user();
        $project = $this->model()->findOrFail($id);

        abort_unless($project->isCreator($authUser->id), 403, 'You are not authorized to delete this project.');

        $project->delete();

        return response()->json([
            'message' => 'Project deleted!'
        ]);
    }
}
