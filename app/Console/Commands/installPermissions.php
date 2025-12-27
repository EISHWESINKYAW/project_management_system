<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class installPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:permissions';

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
        $permissions = config('permissions');
        Permission::truncate();
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $this->info('Permission installation succed');
    }
}
