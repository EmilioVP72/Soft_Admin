<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = DB::table('suppliers')->get();
        $faker = Faker::create('es_MX');

        if ($suppliers->isEmpty()) {
            $this->command->warn('Se requieren proveedores para crear productos.');
            return;
        }

        $units = ['pieza', 'kg', 'litro', 'caja', 'paquete'];
        $products = [];

        for ($i = 0; $i < 50; $i++) {
            $purchasePrice = $faker->randomFloat(2, 5, 500);
            $salePrice = $purchasePrice * $faker->randomFloat(2, 1.2, 2.5); // 20% to 150% margin

            $products[] = [
                'product' => ucfirst($faker->word) . ' ' . $faker->word,
                'barcode' => $faker->unique()->ean13,
                'description' => $faker->sentence,
                'purchase_price' => $purchasePrice,
                'sale_price' => $salePrice,
                'unit' => $faker->randomElement($units),
                'is_active' => $faker->boolean(90), // 90% active
                'fk1_id_supplier' => $suppliers->random()->id_supplier,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('products')->insert($products);
        $this->command->info('Se han creado 50 productos de prueba.');
    }
}
