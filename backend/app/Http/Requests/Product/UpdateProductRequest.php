<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'product' => 'sometimes|required|string|max:255',
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->ignore($id, 'id_product')
            ],
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'fk1_id_supplier' => 'sometimes|required|exists:suppliers,id_supplier',
        ];
    }
}
