<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');
        
        $localities = DB::table('localities')->pluck('id_locality')->toArray();
        
        if (empty($localities)) {
            $this->command->warn('No localities found. Skipping StoreSeeder.');
            return;
        }

        $stores = [
            [
                'store' => 'Tienda Matriz',
                'colony' => 'Centro',
                'street' => 'Madero',
                'exterior_number' => 10,
                'interior_number' => null,
                'reference' => 'Frente al parque',
                'fk1_id_locality' => $faker->randomElement($localities),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'store' => 'Sucursal Roma',
                'colony' => 'Roma Norte',
                'street' => 'Álvaro Obregón',
                'exterior_number' => 200,
                'interior_number' => 12,
                'reference' => 'Edificio azul',
                'fk1_id_locality' => $faker->randomElement($localities),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        for ($i = 0; $i < 3; $i++) {
            $stores[] = [
                'store' => 'Sucursal ' . $faker->city,
                'colony' => 'Colonia ' . $faker->lastName,
                'street' => $faker->streetName,
                'exterior_number' => $faker->buildingNumber,
                'interior_number' => $faker->optional()->buildingNumber,
                'reference' => 'Cerca de ' . $faker->company,
                'fk1_id_locality' => $faker->randomElement($localities),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('stores')->insert($stores);
    }
}
