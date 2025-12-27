<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Http\Resources\RoleListResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleListResource extends JsonResource
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
            'role' => new RoleListResource($this->role),
            'created_at' => $this->updated_at->format('Y-m-d')
        ];
    }
}
