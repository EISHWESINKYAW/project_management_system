<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskDetailResource extends JsonResource
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
            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-M-d'),
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
                'created_by' => $this->project->created_by,
            ],
            'created_by' => $this->creator?->name,
            'created_by_id' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-M-d H:i:s'),
            // 'collaborators' => $this->collaborators->map(function ($user) {
            //     return [
            //         'id' => $user->id,
            //         'name' => $user->name,
            //         'email' => $user->email,
            //         'profile' => $user->profile ? $user->profile->url : null,
            //     ];
            // }),
            'collaborators' => CollaboratorResource::collection($this->collaborators),
        ];
    }
}
