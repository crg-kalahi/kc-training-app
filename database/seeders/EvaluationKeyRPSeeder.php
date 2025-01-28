<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EvaluationKeyRPSeeder extends Seeder
{
    public function run()
    {
        // Get all evaluation training IDs from the evaluation_trainings table
        $evaluationIds = DB::table('evaluation_trainings')->pluck('id')->toArray();

        // Get all learning IDs from the learnings table
        $learningIds = DB::table('learnings')->pluck('id')->toArray();

        // Define possible answers for 'answer' (for example, could be 'yes' or 'no')
        $answers = ['yes', 'no'];

        // Loop through each evaluation ID and assign a learning ID with a random answer
        foreach ($evaluationIds as $evaluationId) {
            $numKeyLearnings = rand(1, 3); // Random number of key learnings (1 to 3) for each evaluation

            for ($i = 1; $i <= $numKeyLearnings; $i++) {
                DB::table('evaluation_key_learnings')->insert([
                    'id' => Str::uuid(), // Generate UUID for the evaluation key learning entry
                    'evaluation_id' => $evaluationId, // Assign current evaluation ID
                    'learning_id' => $learningIds[array_rand($learningIds)], // Randomly assign learning ID
                    'answer' => $answers[array_rand($answers)], // Randomly assign answer (yes or no)
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null // Soft delete set to null for now
                ]);
            }
        }
    }
}
