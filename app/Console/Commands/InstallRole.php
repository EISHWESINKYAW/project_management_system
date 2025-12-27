<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultRoles = [
            'Supervisor',
            'Colaborator',
            'Developer',
            'Coordinator'
        ];

        collect($defaultRoles)->map(function ($role) {
            Role::create([
                'name' => $role,
                'slug' => Str::slug($role)
            ]);
        });

        $this->info('Role insertion succed');
    }
}
