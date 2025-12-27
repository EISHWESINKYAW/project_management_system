<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;

class UserRolePermissionRepository
{

    public function model()
    {
        return User::query();
    }

    public function list($request)
    {
        $request->validate([
            'per_page' => 'nullable|integer'
        ]);

        $userName = $request->input('user_name');
        $roleName = $request->input('role_name');
        $perPage = $request->input('per_page', 15);

        return $this->model()
            ->withWhereHas('role', function ($query) use ($roleName) {
                $query->when($roleName, function ($q) use ($roleName) {
                    $q->where('name', 'ilike', '%' . $roleName . '%')
                        ->orWhere('slug', 'ilike', '%' . $roleName . '%');
                });
            })
            ->when($userName, function ($query) use ($userName) {
                $query->where('name', 'ilike', '%' . $userName . '%');
            })->paginate($perPage);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|numeric',
            'user_id' => 'required|numeric'
        ], [
            'role_id.required' => 'Please select role first',
            'user_id.required' => 'Please select user first'
        ]);

        $permissions = $request->input('permissions');
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');

        $user = User::find($userId);

        abort_unless($user, 404, 'User not found!');

        abort_unless(!$user->role_id, 422, 'Current user already has a role, please update existing one');

        $user->update([
            'role_id' => $roleId
        ]);

        abort_unless($permissions, 422, 'Cannot procress with given permissions');

        foreach ($permissions as $id => $permission) {
            $user->permissions()->attach($id, [
                'create' => $permission['create'],
                'detail' => $permission['detail'],
                'update' => $permission['update'],
                'edit' => $permission['edit'],
                'delete' => $permission['delete']
            ]);
        }

        return $user;
    }

    public function detail($id)
    {
        return $this->model()->with('role', 'permissions')->where('id', $id)->first();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|numeric',
            'user_id' => 'required|numeric'
        ], [
            'role_id.required' => 'Please select role first',
            'user_id.required' => 'Please select user first'
        ]);

        $permissions = $request->input('permissions');
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');

        $user = User::find($userId);

        abort_unless($user, 404, 'User not found!');

        $user->update([
            'role_id' => $roleId
        ]);

        abort_unless($permissions, 422, 'Cannot procress with given permissions');

        $formatPermissions = [];
        foreach ($permissions as $id => $permission) {
            $formatPermissions[$id] = [
                'create' => data_get($permission, 'create'),
                'detail' => data_get($permission, 'detail'),
                'update' => data_get($permission, 'update'),
                'edit' => data_get($permission, 'edit'),
                'delete' => data_get($permission, 'delete'),
            ];
        }

        $user->permissions()->sync($formatPermissions);

        return $user;
    }
}
