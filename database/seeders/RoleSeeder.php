<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Role::truncate();
        $roles = [
            [
                'name' => 'guest',
                'guard_name' => 'web',
            ],
            [
                'name' => 'staff-admin',
                'guard_name' => 'web',
            ],
               [
                'name' => 'staff',
                'guard_name' => 'web',
            ],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
