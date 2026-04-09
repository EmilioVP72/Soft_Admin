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
            'sales' => 'required|array|min:1',
            'sales.*.fk1_id_store' => 'required|exists:stores,id_store',
            'sales.*.fk2_id_user' => 'required|exists:users,id_user',
            'sales.*.fk3_id_payment' => 'required|exists:payments,id_payment',
            'sales.*.total_amount' => 'required|numeric|min:0',
            'sales.*.notes' => 'nullable|string',
            'sales.*.transaction_date' => 'nullable|date',
            'sales.*.details' => 'required|array|min:1',
            'sales.*.details.*.fk2_id_department' => 'required|exists:departments,id_department',
            'sales.*.details.*.quantity' => 'required|numeric|min:0.01',
            'sales.*.details.*.unit_price' => 'required|numeric|min:0',
            'sales.*.details.*.subtotal' => 'required|numeric|min:0',
        ];
    }
}
