<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StagingUserSeeder extends Seeder
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
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role' => 'staff-admin'
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
                'username' =>  $user['username'],
            ]);
            $createdUser->assignRole($user['role']);
        }
    }
}
