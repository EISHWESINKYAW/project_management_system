<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleListResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleDetailResource;
use App\Http\Resources\RolePermissionResource;

class RoleController extends Controller
{
    public function list(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer'
        ]);

        $name = $request->input('name');
        $perPage = $request->input('per_page', 15);

        $roles = Role::query()
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'ilike', '%' . $name . '%')
                    ->orWhere('slug', 'ilike', '%' . $name . '%');
            })
            ->paginate($perPage);

        return RoleListResource::collection($roles);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::latest()->get();

        return PermissionResource::collection($permissions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name'
        ]);

        $name = $request->input('name');
        $permissions = $request->input('permissions');

        $role = Role::create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        abort_unless($role, 422, 'Error creating role!');

        if ($permissions) {
            foreach ($permissions as $id => $permission) {
                $role->permissions()->attach($id, [
                    'create' => $permission['create'],
                    'detail' => $permission['detail'],
                    'update' => $permission['update'],
                    'edit' => $permission['edit'],
                    'delete' => $permission['delete']
                ]);
            }
        }

        return new RoleListResource($role);
    }

    public function detail($id)
    {
        $role = Role::find($id);

        return new RoleDetailResource($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id
        ], [
            'name.unique' => 'The role name is already taken'
        ]);

        $name = $request->input('name');
        $permissions = $request->input('permissions');

        $role = Role::find($id);

        abort_unless($role, 404, "Couldn't procress with the given data!");

        $role->update([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        $formatPermissions = [];

        if ($permissions) {
            foreach ($permissions as $id => $permission) {
                $formatPermissions[$id] = [
                    'create' => data_get($permission, 'create'),
                    'detail' => data_get($permission, 'detail'),
                    'update' => data_get($permission, 'update'),
                    'edit' => data_get($permission, 'edit'),
                    'delete' => data_get($permission, 'delete'),
                ];
            }
        }

        $role->permissions()->sync($formatPermissions);

        return new RoleListResource($role);
    }

    public function delete($id)
    {
        try {
            $role = Role::find($id);

            abort_unless($role, 404, 'Role not found!!');

            $role->permissions()->detach();

            $role->users()->get()->map(function ($user) {
                $user->update([
                    'role_id' => null
                ]);

                $user->permissions()->detach();
            });

            $role->delete();

            return response()->json([
                'message' => 'success'
            ]);
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public function getRolePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required'
        ]);

        $roleId = $request->input('role_id');

        $role = Role::find($roleId);

        $rolePermission = $role->permissions()->get();

        return RolePermissionResource::collection($rolePermission);
    }
}
