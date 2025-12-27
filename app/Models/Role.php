<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const ROLE_OWNER = 'owner';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_SUPER_ADMIN = 'super-admin';

    public const MANAGEMENT_ROLE = [
        self::ROLE_MANAGER,
        self::ROLE_MANAGER,
        self::ROLE_SUPERVISOR,
        self::ROLE_SUPER_ADMIN
    ];


    protected $fillable = [
        'name',
        'slug',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot('create', 'detail', 'update', 'delete', 'edit');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
