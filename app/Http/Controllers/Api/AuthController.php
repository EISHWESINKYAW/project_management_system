<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\RoleListResource;
use App\Http\Resources\RolePermissionResource;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('profile', 'role'); // Load user profile if needed

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
            'role' => new RoleListResource($user->role),
            'permissions' => RolePermissionResource::collection($user->permissions)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|max:12'
        ]);

        $user = User::find($id);

        abort_unless($user, 404, 'User not found with current data!');

        if (Hash::check($request->input('old_password'), $user->password)) {

            abort_if(Hash::check($request->input('password'), $user->password), 422, 'New password must be different from old password!');

            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);

            return response()->json([
                'message' => 'Password changed successfully!'
            ]);
        } else {
            return response()->json([
                'message' => 'Old password is incorrect!'
            ], 422);
        }
    }
}
