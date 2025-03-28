<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EvaluationTrainingsSeeder extends Seeder
{
    public function run()
    {
        // Get all training IDs from the trainings table
        $trainingIds = DB::table('trainings')->pluck('id')->toArray();

        // Get all office representative IDs from the office_representatives table
        $officeRepIds = DB::table('office_representatives')->pluck('id')->toArray();

        // Define possible sexes for evaluation
        $sexes = ['male', 'female'];

        // Loop through each training and create a random evaluation entry
        foreach ($trainingIds as $trainingId) {
            DB::table('evaluation_trainings')->insert([
                'id' => Str::uuid(), // Generate UUID for evaluation entry
                'training_id' => $trainingId, // Assign the current training ID
                'sex' => $sexes[array_rand($sexes)], // Randomly assign sex
                'office_rep_id' => $officeRepIds[array_rand($officeRepIds)], // Randomly assign an office representative
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null // Soft delete is set to null for now
            ]);
        }
    }
}
