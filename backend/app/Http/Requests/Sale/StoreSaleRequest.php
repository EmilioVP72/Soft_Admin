<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fk1_id_store' => 'required|exists:stores,id_store',
            'fk2_id_user' => 'required|exists:users,id_user',
            'fk3_id_payment' => 'required|exists:payments,id_payment',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'transaction_date' => 'nullable|date',
            
            'details' => 'required|array|min:1',
            'details.*.fk2_id_department' => 'required|exists:departments,id_department',
            'details.*.quantity' => 'required|numeric|min:0.01',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.subtotal' => 'required|numeric|min:0',
        ];
    }
}
