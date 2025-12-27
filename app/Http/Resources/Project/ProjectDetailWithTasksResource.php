<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Task\CollaboratorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDetailWithTasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'created_by' => $this->creator?->name,
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d H:i:s') : null,
            'total_tasks' => $this->totalTasks(),
            'completed_tasks' => $this->completeTasks(),
            'tasks' => ProjectTaskResource::collection($this->tasks),
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'collaborators' => CollaboratorResource::collection($this->collaborators),
        ];
    }
}
