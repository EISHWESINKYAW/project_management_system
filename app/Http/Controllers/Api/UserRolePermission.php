<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserRoleListResource;
use App\Repositories\UserRolePermissionRepository;
use App\Http\Resources\Api\UserRolePermissionDetailResource;

class UserRolePermission extends Controller
{
     protected $repo;

    public function __construct(UserRolePermissionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list(Request $request)
    {
        $userRoles = $this->repo->list($request);

        return UserRoleListResource::collection($userRoles);
    }

    public function store(Request $request)
    {
        $user = $this->repo->store($request);

        return new UserRoleListResource($user);
    }

    public function detail($id)
    {
        $userRolePermissions = $this->repo->detail($id);

        return new UserRolePermissionDetailResource($userRolePermissions);
    }

    public function update(Request $request, $id)
    {
        $user = $this->repo->update($request, $id);

        return new UserRoleListResource($user);
    }
}
