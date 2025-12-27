<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Task\TaskDetailResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'profile_url' => $this->profile ? get_file_url($this->profile->url) : null,
            'password' => $this->password,
            'education' => $this->education,
            'address' => $this->address,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'nrc_no' => $this->nrc_no,
            'role' => $this->role ? new RoleDetailResource($this->role) : null,
            'tasks' => $this->tasks ? TaskDetailResource::collection($this->tasks) : [],
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
