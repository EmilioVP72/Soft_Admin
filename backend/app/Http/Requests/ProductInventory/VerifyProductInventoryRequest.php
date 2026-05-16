<?php

namespace App\Http\Requests\ProductInventory;

use Illuminate\Foundation\Http\FormRequest;

class VerifyProductInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'physical_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
