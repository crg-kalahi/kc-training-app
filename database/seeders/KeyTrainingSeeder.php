<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class KeyTrainingSeeder extends Seeder
{
    public function run()
    {
        // Initialize Faker
        $faker = Faker::create();

        // Generate 100 records with fake data
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'title' => $faker->sentence(3),  // Generates a random sentence with 3 words
                'order' => $i,
                'is_default' => $i % 2 == 0 ? true : false,  // Alternate between true and false for is_default
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert data into the table
        DB::table('key_trainings')->insert($data);
    }
}
