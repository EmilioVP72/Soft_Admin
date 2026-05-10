<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        DB::table('payments')->insert([
            ['payment' => 'Efectivo', 'description' => 'Pago en efectivo', 'created_at' => now(), 'updated_at' => now()],
            ['payment' => 'Tarjeta', 'description' => 'Pago con tarjeta de crédito/débito', 'created_at' => now(), 'updated_at' => now()],
            ['payment' => 'Transferencia', 'description' => 'Transferencia SPEI', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('receipts')->insert([
            ['receipt' => 'Ticket', 'description' => 'Comprobante simple', 'created_at' => now(), 'updated_at' => now()],
            ['receipt' => 'Factura', 'description' => 'Comprobante fiscal', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $suppliers = [
            ['supplier' => 'Distribuidora Global SA', 'created_at' => now(), 'updated_at' => now()],
            ['supplier' => 'Comercializadora Nacional', 'created_at' => now(), 'updated_at' => now()],
        ];

        for ($i = 0; $i < 10; $i++) {
            $suppliers[] = [
                'supplier' => $faker->company . ' ' . $faker->companySuffix,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('suppliers')->insert($suppliers);
    }
}
