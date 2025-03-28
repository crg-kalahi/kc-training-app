<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventFacilitatorsSeeder extends Seeder
{
    public function run()
    {
        // Get all training IDs from the trainings table
        $trainingIds = DB::table('trainings')->pluck('id')->toArray();

        // Get all user IDs from the users table who are eligible to be facilitators
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Loop through each training and generate between 1-2 facilitators
        foreach ($trainingIds as $trainingId) {
            $numFacilitators = rand(1, 2); // Random number of facilitators between 1 and 2
            
            for ($i = 1; $i <= $numFacilitators; $i++) {
                DB::table('event_facilitators')->insert([
                    'training_id' => $trainingId, // Assign current training ID
                    'user_id' => $userIds[array_rand($userIds)], // Randomly assign a user ID as a facilitator
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null // Or you can assign random values for soft deletes
                ]);
            }
        }
    }
}
