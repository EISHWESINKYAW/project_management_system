<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use App\Services\FileUploadService;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
            'name'     => 'nullable|string',
            'email'    => 'nullable|string',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $perPage = $request->input('per_page', 15);

        $users = User::query()
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'ilike', '%' . $name . '%');
            })
            ->with('profile')
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    public function store(Request $request, FileUploadService $fileUploadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|string',
            'nrc_no' => 'nullable|string|max:20',
            'education' => 'nullable|string|max:255',
            'role_id' => 'required|integer',
        ]);

        $profileUrl = null;
        if ($request->hasFile('profile')) {
            $profileUrl = $fileUploadService->upload($request->file('profile'));
        }
        if (!$profileUrl) {
            \Log::error('File uploaded but no URL returned from FileUploadService.');
        }
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'nrc_no' => $validated['nrc_no'] ?? null,
            'education' => $validated['education'] ?? null,
            'role_id' => $validated['role_id'],
        ];

        $user = User::create($userData);

        $permissions = $user->role ? $user->role->permissions : [];

        foreach ($permissions as $permission) {
            $user->permissions()->attach($permission->id, [
                'create' => $permission->pivot->create,
                'detail' => $permission->pivot->detail,
                'update' => $permission->pivot->update,
                'delete' => $permission->pivot->delete,
                'edit'   => $permission->pivot->edit,
            ]);
        }
        
        if ($profileUrl) {
            $user->profile()->create([
                'url' => $profileUrl,
            ]);
        }

        return new UserResource($user);
    }

    public function detail($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(Request $request, FileUploadService $fileUploadService, $id)
    {
        $user = User::findOrFail($id);

        logger(['logging all the update data here', $request->all()]);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'profile' => 'nullable|max:2048',
            // 'password'    => 'nullable|string|min:6',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|string',
            'nrc_no' => 'nullable|string|max:20',
            'education' => 'nullable|string|max:255',
            'role_id'     => 'sometimes|exists:roles,id',
        ]);

        // if (isset($validated['password'])) {
        //     $validated['password'] = Hash::make($validated['password']);
        // }

        $user->update($validated);

        if ($request->hasFile('profile')) {
            logger('has file ');
            $file = $request->file('profile');

            // Remove old profile if exists
            if ($user->profile) {
                $fileUploadService->delete($user->profile->url);
            }

            $profileUrl = $fileUploadService->upload($file);

            $user->profile()->update([
                'url' => $profileUrl,
            ]);
        }

        return new UserResource($user);
    }

    protected function prepareData($request)
    {
        return $request->only([
            'name',
            'email',
            'phone',
            'gender',
            'address',
            'nrc_no',
            'education',
            'password'
        ]);
    }

    public function updateProfile(Request $request, FileUploadService $fileUploadService, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
        ]);

        $data = $this->prepareData($request);

        $user = User::find($id);

        abort_unless($user, 404, 'User not found with current data!');

        $user->update($data);

        if ($request->hasFile('profile')) {
            logger('has file ');
            $file = $request->file('profile');

            // Remove old profile if exists
            if ($user->profile) {
                $fileUploadService->delete($user->profile->url);
            }

            $profileUrl = $fileUploadService->upload($file);

            $user->profile()->update([
                'url' => $profileUrl,
            ]);
        }

        $user->refresh();
        return $user;
    }
}
