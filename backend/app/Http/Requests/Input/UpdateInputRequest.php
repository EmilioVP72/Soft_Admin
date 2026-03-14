<?php

namespace App\Http\Requests\Input;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fk1_id_store' => 'sometimes|exists:stores,id_store',
            'fk2_id_user' => 'sometimes|exists:users,id_user',
            'fk3_id_payment' => 'sometimes|exists:payments,id_payment',
            'total_amount' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
            'transaction_date' => 'nullable|date',
            
            'details' => 'sometimes|array|min:1',
            'details.*.fk2_id_department' => 'required_with:details|exists:departments,id_department',
            'details.*.quantity' => 'required_with:details|numeric|min:0.01',
            'details.*.unit_price' => 'required_with:details|numeric|min:0',
            'details.*.subtotal' => 'required_with:details|numeric|min:0',
        ];
    }
}
