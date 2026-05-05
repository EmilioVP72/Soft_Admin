<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Guadalupe',
            'email' => 'smariaguadalupe256@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '+524151679796',
        ]);

        DB::table('users')->insert([
            'name' => 'Sergio',
            'email' => 'Sergio.cortes.n290399@gmail.com',
            'password' => Hash::make('ArchLinuxLoMejor100%'),
            'phone' => '+524613083341',
        ]);

        DB::table('users')->insert([
            'name' => 'Emilio',
            'email' => 'emiliovpsis@gmail.com',
            'password' => Hash::make('Emilio72@#'),
            'phone' => '+524151805038',
        ]);

    }
}
