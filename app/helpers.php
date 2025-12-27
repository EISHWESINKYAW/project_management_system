<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

if (!function_exists('auth_user')) {
    function auth_user()
    {
        return Auth::user();
    }
}

if (!function_exists('get_file_url')) {
    function get_file_url($path)
    {
        if (!$path) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        return Storage::disk(config('filesystems.default'))->url($path);
    }
}

if (! function_exists('isSuperAdmin')) {
    function isSuperAdmin($user = null)
    {
        $user = $user ?: Auth::user();

        return $user && $user->role && $user->role->slug === 'super-admin';
    }
}
