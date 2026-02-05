<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Store;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Genera datos de prueba para transacciones y detalles de transacciones
     */
    public function run(): void
    {
        // Obtener datos existentes
        $stores = Store::all();
        $users = User::all();
        $departments = Department::all();

        if ($stores->isEmpty() || $users->isEmpty() || $departments->isEmpty()) {
            $this->command->warn('Se requieren tiendas, usuarios y departamentos en la BD antes de ejecutar este seeder.');
            return;
        }

        // Crear 50 transacciones de prueba
        for ($i = 0; $i < 50; $i++) {
            $transaction = Transaction::create([
                'fk1_id_store' => $stores->random()->id_store,
                'fk2_id_user' => $users->random()->id_user,
                'total_amount' => rand(100, 5000),
                'transaction_type' => 'sale',
                'notes' => 'Transacción de prueba #' . ($i + 1),
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);

            // Crear 2-5 detalles para cada transacción
            $detailCount = rand(2, 5);
            $totalSubtotal = 0;

            for ($j = 0; $j < $detailCount; $j++) {
                $quantity = rand(1, 10);
                $unitPrice = rand(10, 1000);
                $subtotal = $quantity * $unitPrice;

                TransactionDetail::create([
                    'fk1_id_transaction' => $transaction->id_transaction,
                    'fk2_id_department' => $departments->random()->id_department,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $totalSubtotal += $subtotal;
            }

            // Actualizar el total de la transacción
            $transaction->update(['total_amount' => $totalSubtotal]);
        }

        $this->command->info('Se han creado 50 transacciones de prueba correctamente.');
    }
}
