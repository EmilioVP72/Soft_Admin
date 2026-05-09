<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductInventorySeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->get();
        $faker = Faker::create('es_MX');

        if ($products->isEmpty()) {
            $this->command->warn('Se requieren productos para crear inventarios.');
            return;
        }

        $statuses = ['pending', 'verified', 'discrepancy'];
        $inventories = [];

        foreach ($products as $product) {
            // Generar 1-3 registros de inventario por producto
            $recordsCount = rand(1, 3);
            for ($i = 0; $i < $recordsCount; $i++) {
                $ticketQuantity = $faker->randomFloat(2, 10, 500);
                
                $status = $faker->randomElement($statuses);
                
                $physicalQuantity = null;
                $difference = null;
                $verifiedAt = null;

                if ($status !== 'pending') {
                    $physicalQuantity = $status === 'verified' 
                        ? $ticketQuantity 
                        : $ticketQuantity + $faker->randomFloat(2, -10, 10);
                    $difference = $physicalQuantity - $ticketQuantity;
                    $verifiedAt = $faker->dateTimeBetween('-1 month', 'now');
                }

                $inventories[] = [
                    'fk1_id_product' => $product->id_product,
                    'ticket_quantity' => $ticketQuantity,
                    'physical_quantity' => $physicalQuantity,
                    'difference' => $difference,
                    'status' => $status,
                    'notes' => $status === 'discrepancy' ? 'Diferencia encontrada en conteo físico.' : null,
                    'ticket_date' => $faker->dateTimeBetween('-3 months', '-1 month'),
                    'verified_at' => $verifiedAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('product_inventories')->insert($inventories);
        $this->command->info('Se han creado registros de inventario de prueba.');
    }
}
