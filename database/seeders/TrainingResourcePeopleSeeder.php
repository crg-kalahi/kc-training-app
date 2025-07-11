<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrainingResourcePeopleSeeder extends Seeder
{

    public function run()
    {
        // Get all training IDs from the trainings table
        $trainingIds = DB::table('trainings')->pluck('id')->toArray();

        // Define some sample data for random generation
        $positions = ['Trainer', 'Guest Speaker', 'Facilitator', 'Instructor', 'Panelist'];
        $genders = ['male', 'female'];
        $extNames = ['Jr.', 'Sr.', 'III', 'IV'];
        $topics = ['Introduction to Project Management', 'Advanced JavaScript Techniques', 'Sustainable Development Practices', 'Data Privacy and Security', 'Effective Leadership in the Workplace'];

        // Randomized lists of first names and last names
        $firstNames = ['John', 'Jane', 'Michael', 'Emma', 'James', 'Sophia', 'David', 'Olivia', 'Daniel', 'Ava'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Rodriguez', 'Martinez'];
        $middleNames = ['Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Lee'];

        // Loop through each training and generate between 1-3 resource people
        foreach ($trainingIds as $trainingId) {
            $numResourcePeople = rand(1, 3); // Random number of resource people between 1 and 3
            
            for ($i = 1; $i <= $numResourcePeople; $i++) {
                DB::table('training_resource_people')->insert([
                    'id' => Str::uuid(), // Generate UUID for resource person
                    'training_id' => $trainingId, // Assign current training ID
                    'is_internal' => rand(0, 1), // Random internal or external (0 or 1)
                    'is_female' => $genders[array_rand($genders)] == 'female' ? 1 : 0, // Random gender (1 = female, 0 = male)
                    'position' => $positions[array_rand($positions)], // Random position
                    'lname' => $lastNames[array_rand($lastNames)], // Random last name
                    'fname' => $firstNames[array_rand($firstNames)], // Random first name
                    'mname' => $middleNames[array_rand($middleNames)], // Random middle name
                    'ext_name' => $extNames[array_rand($extNames)], // Random suffix (e.g., Jr., Sr.)
                    'email' => Str::lower($firstNames[array_rand($firstNames)]) . '.' . Str::lower($lastNames[array_rand($lastNames)]) . rand(1, 99) . '@gmail.com', // Legitimate email format
                    'topic' => $topics[array_rand($topics)], // Random topic
                    'encoded_by' => 'user_' . rand(1, 10), // Assuming you have users with ids 1-10
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null // Or you can assign random values for soft deletes
                ]);
            }
        }
    }
}