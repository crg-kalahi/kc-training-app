<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(KeyTrainingSeeder::class);
        $this->call(KeyResourcePersonSeeder::class);
        $this->call(LearningSeeder::class);
        $this->call(OfficeRepresentativesSeeder::class);
        $this->call(TrainingsTableSeeder::class);
        $this->call(TrainingParticipantsSeeder::class);
        $this->call(TrainingResourcePeopleSeeder::class);
        $this->call(EventFacilitatorsSeeder::class);
        $this->call(EvaluationTrainingsSeeder::class);
        $this->call(EvaluationKeyAreaSeeder::class);
        $this->call(EvaluationLearningsSeeder::class);
        $this->call(EvaluationKeyRPSeeder::class);
        // $this->call(EvaluationKeyAreaSeeder::class);
    }
}
