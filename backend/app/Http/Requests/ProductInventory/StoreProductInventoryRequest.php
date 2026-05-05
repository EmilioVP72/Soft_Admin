<?php

namespace App\Http\Requests\ProductInventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fk1_id_product' => 'required|exists:products,id_product',
            'ticket_quantity' => 'required|numeric|min:0',
            'physical_quantity' => 'nullable|numeric|min:0',
            'difference' => 'nullable|numeric',
            'status' => 'nullable|in:pending,verified,discrepancy',
            'notes' => 'nullable|string',
            'ticket_date' => 'nullable|date',
        ];
    }
}
