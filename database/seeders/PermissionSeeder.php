<?php

namespace Database\Seeders;

use App\Models\Role;
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
        // Clear relevant caches
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('cache:forget spatie.permission.cache');

        // Temporarily disable foreign key constraints for truncation
        Schema::disableForeignKeyConstraints();

        // Truncate permission tables
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();

        Schema::enableForeignKeyConstraints();
    }

    private function createPermissions()
    {
        $rolePermissions = [
            'staff-admin' => ['staff', config('permissions.staff-admin')],
            'staff' => ['staff', config('permissions.staff')],
            'guest' => ['guest', config('permissions.guest')],
        ];

        foreach ($rolePermissions as $roleName => [$type, $rolePermission]) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->command->warn("Role '{$roleName}' not found. Skipping...");
                continue;
            }

            foreach ($rolePermission as $module => $permissions) {
                if (!is_array($permissions)) {
                    $permissionName = strtoupper(str_replace('-', ' - ', $type)) . ' - ' . $permissions;
                    $this->createPermission($role, $permissionName);
                    continue;
                }

                foreach ($permissions as $permission) {
                    $permissionName = strtoupper(str_replace('-', ' - ', $type)) . ' - ' . $module . ' - ' . $permission;
                    $this->createPermission($role, $permissionName);
                }
            }
        }
    }

    private function createPermission($role, $name)
    {
        $perm = Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web']
        );

        $role->givePermissionTo($perm);
    }
}
