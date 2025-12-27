<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDetailResource extends JsonResource
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
            'collaborators' => $this->collaborators->map(function ($user) {
                return [
                    'id'   => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }),
        ];
    }
}
