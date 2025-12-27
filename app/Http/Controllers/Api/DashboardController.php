<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Project\ProjectDetailWithTasksResource;
use App\Http\Resources\Project\ProjectTaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function latestProjects(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:5'
        ]);

        $authUser = Auth::user();
        $perPage = $request->input('per_page', 3);

        $projects = Project::query()
            ->with([
                'creator',
                'collaborators',
                'tasks' => function ($query) use ($authUser) {
                    $query->isCreatorOrCollaborator($authUser->id);
                }
            ])
            ->isCreatorOrCollaborator($authUser->id)->paginate($perPage);

        return ProjectDetailWithTasksResource::collection($projects);
    }

    public function latestTasks(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:5'
        ]);

        $authUser = Auth::user();
        $perPage = $request->input('per_page', 3);

        $tasks = Task::query()
            ->with([
                'creator',
                'collaborators',
                'project'
            ])
            ->isCreatorOrCollaborator($authUser->id)
            ->paginate($perPage);

        return ProjectTaskResource::collection($tasks);
    }
}
