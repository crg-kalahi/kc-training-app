<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class KeyResourcePersonSeeder extends Seeder
{
    public function run()
    {
        // Initialize Faker
        $faker = Faker::create();

        // Generate 100 records with fake data
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'title' => $faker->name,  // Generate a random name for the title
                'order' => $i,            // Set the order field as the loop index
                'is_default' => $i % 2 == 0 ? true : false,  // Alternate between true and false for is_default
                'deleted_at' => $faker->boolean(20) ? Carbon::now()->subDays(rand(1, 30)) : null, // 20% chance for a deleted_at value
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert data into the table
        DB::table('key_resource_people')->insert($data);
    }
}
