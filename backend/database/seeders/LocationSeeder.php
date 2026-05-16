<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        $countryId = DB::table('countries')->insertGetId([
            'country' => 'México',
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_country');

        $stateId = DB::table('states')->insertGetId([
            'state' => 'Guanajuato',
            'fk1_id_country' => $countryId,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_state');

        $municipalityId = DB::table('municipalities')->insertGetId([
            'municipality' => 'San Miguel de Allende',
            'fk1_id_state' => $stateId,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_municipality');

        $localities = [
            [
                'locality' => 'Zona Centro', 
                'fk1_id_municipality' => $municipalityId, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'locality' => 'Guadalupe', 
                'fk1_id_municipality' => $municipalityId, 
                'created_at' => now(), 
                'updated_at' => now()
            ]
        ];

        for ($i = 0; $i < 5; $i++) {
            $localities[] = [
                'locality' => 'Colonia ' . $faker->lastName,
                'fk1_id_municipality' => $municipalityId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('localities')->insert($localities);
    }
}
