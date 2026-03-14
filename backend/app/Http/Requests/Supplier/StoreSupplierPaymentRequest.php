<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fk2_id_department' => 'required|exists:departments,id_department',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date'
        ];
    }
}
