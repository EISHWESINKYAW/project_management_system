<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Project\ProjectColaboratorResource;
use App\Repositories\ProjectRepository;
use App\Http\Resources\Project\ProjectListResoruce;
use App\Http\Resources\Project\ProjectDetailResource;
use App\Http\Resources\Project\ProjectDetailWithTasksResource;
use App\Http\Resources\ProjectForTaskResource;

class ProjectController extends Controller
{
    protected $repo;

    public function __construct(ProjectRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list(Request $request)
    {
        $projects = $this->repo->list($request);

        return ProjectListResoruce::collection($projects);
    }

    public function listForTaskStore()
    {
        $projects = $this->repo->listForTaskStore();

        return ProjectForTaskResource::collection($projects);
    }

    public function store(Request $request)
    {
        $project = $this->repo->store($request);

        return new ProjectDetailResource($project);
    }

    public function collaborator()
    {
        $projectColaborator = $this->repo->colaborator();

        return ProjectColaboratorResource::collection($projectColaborator);
    }

    public function projectColaborator($id)
    {
        $projectColaborator = $this->repo->projectColaborator($id);

        return ProjectColaboratorResource::collection($projectColaborator);
    }

    public function detail($id)
    {
        $project = $this->repo->detail($id);

        return new ProjectDetailResource($project);
    }

    public function detailWithTasks($id)
    {
        $project = $this->repo->detailWithTasks($id);

        return new ProjectDetailWithTasksResource($project);
    }

    public function update(Request $request, $id)
    {
        $project = $this->repo->update($request, $id);

        return new ProjectDetailResource($project);
    }

    public function makeComplete($id)
    {
        $project = $this->repo->makeComplete($id);

        return response()->json([
            'message' => 'Project marked as complete successfully.',
            'status' => 200,
        ]);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
