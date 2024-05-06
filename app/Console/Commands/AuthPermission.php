<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AuthPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:permission {name} {--R|remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = $this->generatePermissions();

        // check if its remove
        if ($is_remove = $this->option('remove')) {
            // remove permission
            if (Permission::where('name', 'LIKE', '%' . $this->getNameArgument())->delete()) {
                $this->warn('Permissions ' . implode(', ', $permissions) . ' deleted.');
            } else {
                $this->warn('No permissions for ' . $this->getNameArgument() . ' found!');
            }
        } else {
            // create permissions
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            $this->info('Permissions ' . implode(', ', $permissions) . ' created.');
        }

        // sync role for admin
        if ($role = Role::where('name', 'Admin')->first()) {
            $role->syncPermissions(Permission::all());
            $this->info('Admin permissions');
        }
    }

    private function generatePermissions()
    {
        $abilities = ['view', 'add', 'edit', 'delete'];
        $name = $this->getNameArgument();

        return array_map(function ($val) use ($name) {
            return $val . '_' . $name;
        }, $abilities);
    }

    private function getNameArgument()
    {
        return strtolower(Str::plural($this->argument('name')));
    }
}
