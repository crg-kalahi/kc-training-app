<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $initUsers = [
            [
                'id' => Str::uuid(),
                'fname' => 'admin',
                'mname' => 'admin',
                'lname' => 'admin',
                'id_number' => '16-0000',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password')

            ]
        ];  

        foreach($initUsers as $user){
            $user = User::create([
                "id" =>  $user['id'],
                'email' => $user['email'],
                'password' => $user['password'], // You may adjust this as needed
                "fname" =>  $user['fname'],
                "mname" =>  $user['mname'],
                "lname" =>  $user['lname'],
                "id_number" =>  $user['id_number'],
                "password" =>  $user['password'],
            ]);
            $user->assignRole('staff-admin');
        }
        
    }
}
