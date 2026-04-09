<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $locality = DB::table('localities')->first();
        $allLocalities = DB::table('localities')->get();
        
        if (!$locality) {
            $this->command->warn('No localities found. Skipping StoreSeeder.');
            return;
        }

        DB::table('stores')->insert([
            [
                'store' => 'Tienda Matriz',
                'colony' => 'Centro',
                'street' => 'Madero',
                'exterior_number' => 10,
                'interior_number' => null,
                'reference' => 'Frente al parque',
                'fk1_id_locality' => $locality->id_locality,
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
                'fk1_id_locality' => count($allLocalities) > 1 ? $allLocalities[1]->id_locality : $locality->id_locality,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
