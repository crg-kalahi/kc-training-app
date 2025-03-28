<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrainingParticipantsSeeder extends Seeder
{
    public function run()
    {
        // Get all training IDs from the trainings table
        $trainingIds = DB::table('trainings')->pluck('id')->toArray();

        // Define some sample data for random generation
        $positions = ['Trainer', 'Participant', 'Facilitator', 'Guest Speaker'];
        $municipalities = ['Municipality A', 'Municipality B', 'Municipality C', 'Municipality D'];
        $barangays = ['Brgy 1', 'Brgy 2', 'Brgy 3', 'Brgy 4'];
        $genders = ['male', 'female'];
        $extNames = ['Jr.', 'Sr.', 'III', 'IV'];

        // Loop through each training and generate between 20-50 participants
        foreach ($trainingIds as $trainingId) {
            $numParticipants = rand(20, 50); // Random number of participants between 20 and 50
            
            for ($i = 1; $i <= $numParticipants; $i++) {
                DB::table('training_participants')->insert([
                    'id' => Str::uuid(), // Generate UUID for participant
                    'training_id' => $trainingId, // Assign current training ID
                    'email' => 'participant' . $i . '@example.com', // Simple email format
                    'municipality' => $municipalities[array_rand($municipalities)], // Random municipality
                    'brgy' => $barangays[array_rand($barangays)], // Random barangay
                    'is_internal' => rand(0, 1), // Random internal or external (0 or 1)
                    'is_female' => $genders[array_rand($genders)] == 'female' ? 1 : 0, // Random gender (1 = female, 0 = male)
                    'position' => $positions[array_rand($positions)], // Random position
                    'lname' => 'LastName' . $i, // Last name
                    'fname' => 'FirstName' . $i, // First name
                    'mname' => 'MiddleName' . $i, // Middle name
                    'ext_name' => $extNames[array_rand($extNames)], // Random suffix (e.g., Jr., Sr.)
                    'pre_test' => rand(0, 100), // Random pre-test score
                    'post_test' => rand(0, 100), // Random post-test score
                    'encoded_by' => 'user_' . rand(1, 10), // Assuming you have users with ids 1-10
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null // Or you can assign random values for soft deletes
                ]);
            }
        }
    }
}
