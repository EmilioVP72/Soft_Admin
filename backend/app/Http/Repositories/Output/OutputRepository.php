<?php

namespace App\Http\Repositories\Output;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class OutputRepository
{
    public function getAll()
    {
        return Transaction::with(['store', 'user', 'payment', 'details.department'])
            ->where('transaction_type', 'output')
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function find($id)
    {
        return Transaction::with(['store', 'user', 'payment', 'details.department'])
            ->where('transaction_type', 'output')
            ->where('id_transaction', $id)
            ->first();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $transactionData = [
                'fk1_id_store' => $data['fk1_id_store'],
                'fk2_id_user' => $data['fk2_id_user'],
                'fk3_id_payment' => $data['fk3_id_payment'],
                'total_amount' => $data['total_amount'],
                'transaction_type' => 'output',
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
            ];

            $transaction = Transaction::create($transactionData);

            foreach ($data['details'] as $detail) {
                $transaction->details()->create([
                    'fk2_id_department' => $detail['fk2_id_department'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            return $transaction->load(['store', 'user', 'payment', 'details.department']);
        });
    }

    public function update(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            $transactionData = [];
            if (isset($data['fk1_id_store'])) $transactionData['fk1_id_store'] = $data['fk1_id_store'];
            if (isset($data['fk2_id_user'])) $transactionData['fk2_id_user'] = $data['fk2_id_user'];
            if (isset($data['fk3_id_payment'])) $transactionData['fk3_id_payment'] = $data['fk3_id_payment'];
            if (isset($data['total_amount'])) $transactionData['total_amount'] = $data['total_amount'];
            if (isset($data['notes'])) $transactionData['notes'] = $data['notes'];
            if (isset($data['transaction_date'])) $transactionData['transaction_date'] = $data['transaction_date'];

            if (!empty($transactionData)) {
                $transaction->update($transactionData);
            }

            if (isset($data['details'])) {
                $transaction->details()->delete();
                foreach ($data['details'] as $detail) {
                    $transaction->details()->create([
                        'fk2_id_department' => $detail['fk2_id_department'],
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'],
                        'subtotal' => $detail['subtotal'],
                    ]);
                }
            }

            return $transaction->load(['store', 'user', 'payment', 'details.department']);
        });
    }

    public function delete(Transaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->details()->delete();
            return $transaction->delete();
        });
    }
}
