<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EvaluationKeyAreaSeeder extends Seeder
{
    public function run()
    {
        // Get all evaluation training IDs from the evaluation_trainings table
        $evaluationIds = DB::table('evaluation_trainings')->pluck('id')->toArray();

        // Get all key training IDs from the key_trainings table
        $areaTrainingIds = DB::table('key_trainings')->pluck('id')->toArray();

        // Define possible states for 'stat' (for example, could be 'pass' or 'fail')
        $stats = ['pass', 'fail'];

        // Loop through each evaluation ID and assign an area training ID with a random stat
        foreach ($evaluationIds as $evaluationId) {
            $numKeyAreas = rand(1, 3); // Random number of key areas (1 to 3) for each evaluation

            for ($i = 1; $i <= $numKeyAreas; $i++) {
                DB::table('evaluation_key_areas')->insert([
                    'id' => Str::uuid(), // Generate UUID for the evaluation key area entry
                    'evaluation_id' => $evaluationId, // Assign current evaluation ID
                    'area_training_id' => $areaTrainingIds[array_rand($areaTrainingIds)], // Randomly assign area training ID
                    'stat' => $stats[array_rand($stats)], // Randomly assign stat (pass or fail)
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null // Soft delete set to null for now
                ]);
            }
        }
    }
}
