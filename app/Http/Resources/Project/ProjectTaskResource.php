<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Task\CollaboratorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTaskResource extends JsonResource
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
            'collaborators' => CollaboratorResource::collection($this->collaborators),
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d H:i:s') : null,
            'created_by' => $this->creator?->name,
        ];
    }
}
