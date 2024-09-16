<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->initialize();
        $this->createPermissions();
    }

    private function initialize()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('cache:forget spatie.permission.cache');
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        Schema::disableForeignKeyConstraints();
    }

    private function createPermissions()
    {

        $rolePermissions = [
            'staff-admin' => ['staff', config('permissions.staff')],
            'guest' => ['guest', config('permissions.guest')],
        ];

        foreach ($rolePermissions as $role => $values)
        {
            $role = Role::where('name', $role)->first();

            list($type, $rolePermission) = $values;

            foreach ($rolePermission as $module => $permissions)
            {
                if(!is_array($permissions)) {
                    $permissionName = strtoupper(str_replace('-', ' - ', $type)) . ' - ' . $permissions;
                    $this->createPermission($role, $type, $permissionName, $permissions);
                    continue;
                }
                foreach ($permissions as $permission) {
                    $permissionName = strtoupper(str_replace('-', ' - ', $type)) .' - '. $module . ' - ' . $permission;

                    $this->createPermission($role, $type, $permissionName, $permission);
                }
            }
        }
    }

    private function createPermission($role, $type, $name, $permission)
    {
        $perm = Permission::where('name', $name)->first();
        if(!$perm) {
            $perm = Permission::create([
                'name' => $name,
                'guard_name' => 'users',
            ]);
        }

        $role->givePermissionTo($perm);
    }
}
