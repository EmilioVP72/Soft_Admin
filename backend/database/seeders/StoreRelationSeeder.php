<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreRelationSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->get();
        $users = DB::table('users')->get();
        $payments = DB::table('payments')->get();
        $receipts = DB::table('receipts')->get();

        if ($stores->isEmpty()) {
            $this->command->warn('No stores found. Skipping StoreRelationSeeder.');
            return;
        }

        foreach ($stores as $store) {
            // Relate with first user if exists
            if ($users->isNotEmpty()) {
                $userStoreExists = DB::table('user_stores')
                    ->where('fk1_id_user', $users->first()->id_user)
                    ->where('fk2_id_store', $store->id_store)
                    ->exists();
                
                if (!$userStoreExists) {
                    DB::table('user_stores')->insert([
                        'salary' => 15000.00,
                        'fk1_id_user' => $users->first()->id_user,
                        'fk2_id_store' => $store->id_store,
                    ]);
                }
            }

            // Relate with payments
            foreach ($payments as $payment) {
                $paymentTypeExists = DB::table('payment_types')
                    ->where('fkl_id_store', $store->id_store)
                    ->where('fk2_id_payment', $payment->id_payment)
                    ->exists();
                
                if (!$paymentTypeExists) {
                    DB::table('payment_types')->insert([
                        'type' => $payment->payment,
                        'fkl_id_store' => $store->id_store,
                        'fk2_id_payment' => $payment->id_payment,
                    ]);
                }
            }

            // Relate with receipts
            foreach ($receipts as $receipt) {
                $receiptTypeExists = DB::table('receipt_types')
                    ->where('fkl_id_store', $store->id_store)
                    ->where('fk2_id_receipt', $receipt->id_receipt)
                    ->exists();

                if (!$receiptTypeExists) {
                    DB::table('receipt_types')->insert([
                        'type' => $receipt->receipt,
                        'fkl_id_store' => $store->id_store,
                        'fk2_id_receipt' => $receipt->id_receipt,
                    ]);
                }
            }
        }
    }
}
