<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payments')->insert([
            ['payment' => 'Efectivo', 'description' => 'Pago en efectivo', 'created_at' => now(), 'updated_at' => now()],
            ['payment' => 'Tarjeta', 'description' => 'Pago con tarjeta de crédito/débito', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('receipts')->insert([
            ['receipt' => 'Ticket', 'description' => 'Comprobante simple', 'created_at' => now(), 'updated_at' => now()],
            ['receipt' => 'Factura', 'description' => 'Comprobante fiscal', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('suppliers')->insert([
            ['supplier' => 'Distribuidora Global SA', 'created_at' => now(), 'updated_at' => now()],
            ['supplier' => 'Comercializadora Nacional', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
