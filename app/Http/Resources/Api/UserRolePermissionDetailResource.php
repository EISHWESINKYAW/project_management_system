<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleListResource;
use App\Http\Resources\RolePermissionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRolePermissionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this),
            'role' => new RoleListResource($this->role),
            'permissions' => RolePermissionResource::collection($this->permissions)
        ];
    }
}
