<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Store;
use App\Models\User;
use App\Models\Department;
use App\Models\Payments;
use Illuminate\Database\Seeder;

class OutputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Genera datos de prueba para Salidas (Outputs)
     */
    public function run(): void
    {
        $stores = Store::all();
        $users = User::all();
        $departments = Department::all();
        $payments = Payments::all();

        if ($stores->isEmpty() || $users->isEmpty() || $departments->isEmpty() || $payments->isEmpty()) {
            $this->command->warn('Se requieren tiendas, usuarios, departamentos y pagos para ejecutar OutputSeeder.');
            return;
        }

        // Crear 15 Salidas (Outputs)
        for ($i = 0; $i < 15; $i++) {
            $transaction = Transaction::create([
                'fk1_id_store' => $stores->random()->id_store,
                'fk2_id_user' => $users->random()->id_user,
                'fk3_id_payment' => $payments->random()->id_payment,
                'total_amount' => 0,
                'transaction_type' => 'output',
                'notes' => 'Salida de mercancía de prueba #' . ($i + 1),
                'transaction_date' => now()->subDays(rand(0, 30)),
            ]);

            $detailCount = rand(2, 6);
            $totalSubtotal = 0;

            for ($j = 0; $j < $detailCount; $j++) {
                $quantity = rand(1, 20); // Generalmente menos cantidad que en entradas
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

            $transaction->update(['total_amount' => $totalSubtotal]);
        }

        $this->command->info('Se han creado 15 Salidas de prueba (Outputs) correctamente.');
    }
}
