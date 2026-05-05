<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'fk1_id_supplier' => 'required|exists:suppliers,id_supplier',
        ];
    }
}
