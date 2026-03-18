<?php

namespace App\Http\Requests\GeneralDepartment;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'g_departament' => 'required|string|max:255',
            'g_descripcion' => 'required|string',
            'fkl_id_tienda' => 'required|integer|exists:stores,id_store',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'g_departament.required' => 'El nombre del departamento general es obligatorio',
            'g_departament.string' => 'El nombre del departamento general debe ser texto',
            'g_departament.max' => 'El nombre del departamento general no debe exceder los 255 caracteres',
            'g_descripcion.required' => 'La descripción es obligatoria',
            'g_descripcion.string' => 'La descripción debe ser texto',
            'fkl_id_tienda.required' => 'El ID de la tienda es obligatorio',
            'fkl_id_tienda.integer' => 'El ID de la tienda debe ser un número entero',
            'fkl_id_tienda.exists' => 'La tienda seleccionada no existe en la base de datos',
        ];
    }
}
