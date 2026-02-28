<?php

namespace App\Http\Requests\Locality;

use Illuminate\Foundation\Http\FormRequest;

class LocalityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locality' => 'required|string|max:255',
            'fk1_id_municipality' => 'required|integer|exists:municipalities,id_municipality',
        ];
    }

    public function messages(): array
    {
        return [
            'locality.required' => 'El nombre de la localidad es obligatorio.',
            'locality.string' => 'El nombre de la localidad debe ser un texto.',
            'locality.max' => 'El nombre de la localidad no puede exceder los 255 caracteres.',
            'fk1_id_municipality.required' => 'Debe seleccionar un municipio.',
            'fk1_id_municipality.integer' => 'El ID del municipio debe ser un número entero.',
            'fk1_id_municipality.exists' => 'El municipio seleccionado no existe.',
        ];
    }
}
