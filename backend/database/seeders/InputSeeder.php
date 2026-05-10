<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class InputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Genera datos de prueba para Entradas (Inputs)
     */
    public function run(): void
    {
        $stores = DB::table('stores')->get();
        $users = DB::table('users')->get();
        $departments = DB::table('departments')->get();
        $payments = DB::table('payments')->get();
        $faker = Faker::create('es_MX');

        if ($stores->isEmpty() || $users->isEmpty() || $departments->isEmpty() || $payments->isEmpty()) {
            $this->command->warn('Se requieren tiendas, usuarios, departamentos y pagos para ejecutar InputSeeder.');
            return;
        }

        // Crear 15 Entradas (Inputs)
        for ($i = 0; $i < 15; $i++) {
            $transactionId = DB::table('transactions')->insertGetId([
                'fk1_id_store' => $stores->random()->id_store,
                'fk2_id_user' => $users->random()->id_user,
                'fk3_id_payment' => $payments->random()->id_payment,
                'total_amount' => 0,
                'transaction_type' => 'input',
                'notes' => 'Entrada de mercancía de prueba #' . ($i + 1),
                'transaction_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_transaction');

            $detailCount = rand(2, 6);
            $totalSubtotal = 0;

            $details = [];
            for ($j = 0; $j < $detailCount; $j++) {
                $quantity = rand(10, 100);
                $unitPrice = rand(10, 1000);
                $subtotal = $quantity * $unitPrice;

                $details[] = [
                    'fk1_id_transaction' => $transactionId,
                    'fk2_id_department' => $departments->random()->id_department,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalSubtotal += $subtotal;
            }

            DB::table('transaction_details')->insert($details);

            DB::table('transactions')
                ->where('id_transaction', $transactionId)
                ->update(['total_amount' => $totalSubtotal]);
        }

        $this->command->info('Se han creado 15 Entradas de prueba (Inputs) correctamente.');
    }
}
