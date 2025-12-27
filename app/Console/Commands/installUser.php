<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class installUser extends Command
{
    protected $signature = 'install:user';
    protected $description = 'Install default users, roles, and permissions';

    public function handle()
    {
        $this->info('Installing user...');

        $user = User::create([
            'name' => 'Lucifer',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('secret'),
            'phone' => '09-123456789',
            'education' => 'University of Yangon',
            'address' => 'Yangon, Myanmar',
        ]);

        $user->profile()->create([
            'url' => 'https://randomuser.me/api/portraits/men/1.jpg'
        ]);

        $this->info('User created: ' . $user->name);

        $this->info('Assigning role to user...');

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin'
        ]);

        $user->update([
            'role_id' => $role->id
        ]);

        $this->info('Role assigned: ' . $role->name);

        $this->info('Assigning permissions to role...');

        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->error('No permissions found. Please install permissions first.');
            return;
        }

        foreach ($permissions as $permission) {
            $id = $permission->id;
            $permissionData = [
                'create' => true,
                'detail' => true,
                'update' => true,
                'edit' => true,
                'delete' => true
            ];

            $role->permissions()->attach($id, $permissionData);
            $user->permissions()->attach($id, $permissionData);
        }

        $this->info('User installation completed successfully.');

        // Supervisor
        $supervisor = User::create([
            'name' => 'Denver',
            'email' => 'denver@gmail.com',
            'password' => Hash::make('secret'),
            'role_id' => 1,
            'phone' => '09-987654321',
            'education' => 'Mandalay Technological University',
            'address' => 'Mandalay, Myanmar',
        ]);
        $supervisor->profile()->create([
            'url' => 'https://randomuser.me/api/portraits/men/2.jpg'
        ]);

        // Collaborator
        $colaborator = User::create([
            'name' => 'Aye Aye',
            'email' => 'ayeaye@gmail.com',
            'password' => Hash::make('secret'),
            'role_id' => 2,
            'phone' => '09-555666777',
            'education' => 'Dagon University',
            'address' => 'Bago, Myanmar',
        ]);
        $colaborator->profile()->create([
            'url' => 'https://randomuser.me/api/portraits/women/3.jpg'
        ]);

        // Developer
        $developer = User::create([
            'name' => 'Elliot Alderson',
            'email' => 'elliot@gmail.com',
            'password' => Hash::make('secret'),
            'role_id' => 3,
            'phone' => '09-888999000',
            'education' => 'Technological University of Hmawbi',
            'address' => 'Naypyidaw, Myanmar',
        ]);
        $developer->profile()->create([
            'url' => 'https://randomuser.me/api/portraits/men/4.jpg'
        ]);

        // Coordinator
        $coordinator = User::create([
            'name' => 'Clowe',
            'email' => 'clowe@gmail.com',
            'password' => Hash::make('secret'),
            'role_id' => 4,
            'phone' => '09-222333444',
            'education' => 'University of Mandalay',
            'address' => 'Taunggyi, Myanmar',
        ]);
        $coordinator->profile()->create([
            'url' => 'https://randomuser.me/api/portraits/women/5.jpg'
        ]);
    }
}
