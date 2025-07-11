<?php

namespace Database\Seeders;

use App\Models\Attachments;
use App\Models\Training;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AttachementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    //  f62c567b-03ce-4808-b7db-cf56f1fc5999
    public function run()
    {
        for($i = 0; $i < 10; $i++) {
            Attachments::create([
                'training_id' => 'f62c567b-03ce-4808-b7db-cf56f1fc5999',
                'name' => 'test file # ' . $i,
                'file_path' => 'file_path # ' . $i,
                'description' => 'test file # ' . $i,
                'type' => 'file type',
            ]);
        }
    }
}
