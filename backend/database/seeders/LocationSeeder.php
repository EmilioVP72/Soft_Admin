<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'country' => 'México',
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_country');

        $stateId = DB::table('states')->insertGetId([
            'state' => 'Ciudad de México',
            'fk1_id_country' => $countryId,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_state');

        $municipalityId = DB::table('municipalities')->insertGetId([
            'municipality' => 'Cuauhtémoc',
            'fk1_id_state' => $stateId,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_municipality');

        DB::table('localities')->insert([
            [
                'locality' => 'Centro Histórico', 
                'fk1_id_municipality' => $municipalityId, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'locality' => 'Roma Norte', 
                'fk1_id_municipality' => $municipalityId, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }
}
