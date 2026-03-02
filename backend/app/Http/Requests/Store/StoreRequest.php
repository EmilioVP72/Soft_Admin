<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $storeId = $this->route('id');

        switch ($this->method()) {
            case 'POST':
                return [
                    'store' => 'required|string|max:255|unique:stores,store',
                    'colony' => 'required|string|max:255',
                    'street' => 'required|string|max:255',
                    'exterior_number' => 'required|string|max:50',
                    'interior_number' => 'nullable|string|max:50',
                    'reference' => 'nullable|string|max:500',
                    'fk1_id_locality' => 'required|integer|exists:localities,id_locality',
                ];

            case 'PUT':
                return [
                    'store' => 'sometimes|required|string|max:255|unique:stores,store,' . $storeId . ',id_store',
                    'colony' => 'sometimes|required|string|max:255',
                    'street' => 'sometimes|required|string|max:255',
                    'exterior_number' => 'sometimes|required|string|max:50',
                    'interior_number' => 'nullable|string|max:50',
                    'reference' => 'nullable|string|max:500',
                    'fk1_id_locality' => 'sometimes|required|integer|exists:localities,id_locality',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'store.required' => 'El nombre de la tienda es obligatorio.',
            'store.string' => 'El nombre de la tienda debe ser una cadena de texto.',
            'store.max' => 'El nombre de la tienda no debe superar los 255 caracteres.',
            'store.unique' => 'El nombre de la tienda ya está registrado.',
            'colony.required' => 'La colonia es obligatoria.',
            'colony.string' => 'La colonia debe ser una cadena de texto.',
            'colony.max' => 'La colonia no debe superar los 255 caracteres.',
            'street.required' => 'La calle es obligatoria.',
            'street.string' => 'La calle debe ser una cadena de texto.',
            'street.max' => 'La calle no debe superar los 255 caracteres.',
            'exterior_number.required' => 'El número exterior es obligatorio.',
            'exterior_number.string' => 'El número exterior debe ser una cadena de texto.',
            'exterior_number.max' => 'El número exterior no debe superar los 50 caracteres.',
            'interior_number.string' => 'El número interior debe ser una cadena de texto.',
            'interior_number.max' => 'El número interior no debe superar los 50 caracteres.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe superar los 500 caracteres.',
            'fk1_id_locality.required' => 'La localidad es obligatoria.',
            'fk1_id_locality.integer' => 'La localidad debe ser un valor válido.',
            'fk1_id_locality.exists' => 'La localidad seleccionada no existe.',
        ];
    }
}
