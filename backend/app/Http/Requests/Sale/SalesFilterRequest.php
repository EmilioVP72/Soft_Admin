<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class SalesFilterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'store_id' => 'sometimes|integer|exists:stores,id_store',
            'department_id' => 'sometimes|integer|exists:departments,id_department',
            'start_date' => 'sometimes|date|before_or_equal:end_date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ];
    }

    public function messages()
    {
        return [
            'store_id.integer' => 'El ID de la tienda debe ser un número entero.',
            'store_id.exists' => 'La tienda seleccionada no existe.',
            'department_id.integer' => 'El ID del departamento debe ser un número entero.',
            'department_id.exists' => 'El departamento seleccionado no existe.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha final.',
            'end_date.date' => 'La fecha final debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
        ];
    }
}
