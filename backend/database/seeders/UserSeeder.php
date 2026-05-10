<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '+524151805038',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Sergio',
            'email' => 'Sergio.cortes.n290399@gmail.com',
            'password' => Hash::make('ArchLinuxLoMejor100%'),
            'phone' => '+524613083341',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Emilio',
            'email' => 'emiliovpsis@gmail.com',
            'password' => Hash::make('Emilio72@#'),
            'phone' => '+524151805039', // Changed last digit to prevent duplicate
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Generate 10 random users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'phone' => $faker->unique()->e164PhoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($users);
    }
}
