<?php

namespace App\Http\Repositories\Payment;

use App\Models\Payments;

class PaymentRepository
{
    public function getAll()
    {
        return Payments::all();
    }

    public function find($id)
    {
        return Payments::find($id);
    }

    public function create(array $data)
    {
        return Payments::create($data);
    }

    public function update(Payments $payment, array $data)
    {
        $payment->update($data);
        return $payment;
    }

    public function delete(Payments $payment)
    {
        $payment->delete();
    }
}
