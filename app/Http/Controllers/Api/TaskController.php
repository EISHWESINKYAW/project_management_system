<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Task\TaskDetailResource;
use App\Http\Resources\Task\TaskListResource;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $repo;

    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list(Request $request)
    {
        $tasks = $this->repo->list($request);

        return TaskListResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $task = $this->repo->store($request);

        return new TaskDetailResource($task);
    }

    public function detail($id)
    {
        $task = $this->repo->detail($id);

        return new TaskDetailResource($task);
    }

    public function update(Request $request, $id)
    {
        $task = $this->repo->update($request, $id);

        return new TaskDetailResource($task);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = $this->repo->updateStatus($request, $id);

        return response()->json($task);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
