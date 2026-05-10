<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StoreRelationSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->get();
        $users = DB::table('users')->get();
        $payments = DB::table('payments')->get();
        $receipts = DB::table('receipts')->get();
        $faker = Faker::create('es_MX');

        if ($stores->isEmpty()) {
            $this->command->warn('No stores found. Skipping StoreRelationSeeder.');
            return;
        }

        foreach ($stores as $store) {
            // Relate with random users
            $randomUsers = $users->random(min(3, $users->count()));
            
            // Make sure admin (first user) has access to all stores
            $adminUser = $users->first();
            if (!$randomUsers->contains('id_user', $adminUser->id_user)) {
                $randomUsers->push($adminUser);
            }

            foreach ($randomUsers as $user) {
                $userStoreExists = DB::table('user_stores')
                    ->where('fk1_id_user', $user->id_user)
                    ->where('fk2_id_store', $store->id_store)
                    ->exists();
                
                if (!$userStoreExists) {
                    DB::table('user_stores')->insert([
                        'salary' => $faker->randomFloat(2, 5000, 30000),
                        'fk1_id_user' => $user->id_user,
                        'fk2_id_store' => $store->id_store,
                    ]);
                }
            }

            // Relate with random payments (at least 2)
            $randomPayments = $payments->random(min(2, $payments->count()));
            foreach ($randomPayments as $payment) {
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

            // Relate with all receipts
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
