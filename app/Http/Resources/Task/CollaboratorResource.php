<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollaboratorResource extends JsonResource
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
            'role' => $this->role->name,
            // 'profile' => $this->profile ? $this->profile->url : null,
            'profile_url' => $this->profile ? get_file_url($this->profile->url) : null,
            'total_projects' => $this->projects->count(),
            'total_tasks_complete' => $this->completeTasks(),
        ];
    }
}
