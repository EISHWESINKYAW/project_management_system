<?php

namespace App\Http\Resources\Project;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'slug'         => $this->slug,
            'status'       => $this->status,
            'created_at'   => $this->created_at->format('Y-m-d'),
            'created_by'   => $this->creator?->name,
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
