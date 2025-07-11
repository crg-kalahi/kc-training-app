<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StagingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(StagingUserSeeder::class);
        $this->call(KeyResourcePersonSeeder::class);
        $this->call(KeyTrainingSeeder::class);
        $this->call(LearningSeeder::class);
        $this->call(OfficeRepresentativesSeeder::class);
    }
}
