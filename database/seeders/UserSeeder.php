<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Predefined users
        $initUsers = [
            [
                'id' => Str::uuid(),
                'fname' => 'admin',
                'mname' => 'admin',
                'lname' => 'admin',
                'id_number' => '16-0000',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role' => 'staff-admin'
            ],
            [
                'id' => Str::uuid(),
                'fname' => 'user',
                'mname' => 'user',
                'lname' => 'user',
                'id_number' => '16-0001',
                'email' => 'user@user.com',
                'password' => Hash::make('password'),
                'role' => 'guest'
            ]
        ];  

        // Insert predefined users
        foreach($initUsers as $user){
            $createdUser = User::create([
                "id" =>  $user['id'],
                'email' => $user['email'],
                'password' => $user['password'],
                "fname" =>  $user['fname'],
                "mname" =>  $user['mname'],
                "lname" =>  $user['lname'],
                "id_number" =>  $user['id_number'],
            ]);
            $createdUser->assignRole($user['role']);
        }

        // Generate 98 more users using Faker
        $faker = Faker::create();
        for ($i = 0; $i < 98; $i++) {
            $user = [
                'id' => Str::uuid(),
                'fname' => $faker->firstName,
                'mname' => $faker->firstName,
                'lname' => $faker->lastName,
                'id_number' => '16-' . str_pad($i + 2, 4, '0', STR_PAD_LEFT),
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => $i % 2 === 0 ? 'staff-admin' : 'guest' // Alternate between 'staff-admin' and 'guest' roles
            ];

            $createdUser = User::create([
                "id" =>  $user['id'],
                'email' => $user['email'],
                'password' => $user['password'],
                "fname" =>  $user['fname'],
                "mname" =>  $user['mname'],
                "lname" =>  $user['lname'],
                "id_number" =>  $user['id_number'],
            ]);
            $createdUser->assignRole($user['role']);
        }
    }
}
