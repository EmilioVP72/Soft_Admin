<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
            'department' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'fk1_id_general_dep' => 'sometimes|required|integer|exists:general_deps,id_general_dep',
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
            'department.required' => 'El nombre del departamento es obligatorio',
            'department.string' => 'El nombre del departamento debe ser texto',
            'department.max' => 'El nombre del departamento no debe exceder los 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'La descripción debe ser texto',
            'fk1_id_general_dep.required' => 'El ID del departamento general es obligatorio',
            'fk1_id_general_dep.integer' => 'El ID del departamento general debe ser un número entero',
            'fk1_id_general_dep.exists' => 'El departamento general seleccionado no existe',
        ];
    }
}
