<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $employeeId = $this->route('id');

        switch ($this->method()) {
            case 'POST':
                return [
                    'full_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:employees,email',
                    'phone' => 'nullable|string|max:20',
                    'document_type' => 'required|string|in:DNI,RUC,Pasaporte,Otro',
                    'document_number' => 'required|string|unique:employees,document_number|max:50',
                    'position' => 'required|string|in:Manager,Supervisor,Cashier,Stock,Sales,Other',
                    'salary' => 'required|numeric|min:0|max:999999.99',
                    'status' => 'required|string|in:Active,Inactive,On Leave',
                    'hire_date' => 'required|date|date_format:Y-m-d',
                    'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:hire_date',
                    'fk_id_user' => 'nullable|integer|exists:users,id_user',
                    'fk_id_store' => 'required|integer|exists:stores,id_store',
                    'notes' => 'nullable|string|max:1000',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'full_name' => 'sometimes|required|string|max:255',
                    'email' => 'sometimes|required|email|unique:employees,email,' . $employeeId . ',id_employee',
                    'phone' => 'nullable|string|max:20',
                    'document_type' => 'sometimes|required|string|in:DNI,RUC,Pasaporte,Otro',
                    'document_number' => 'sometimes|required|string|unique:employees,document_number,' . $employeeId . ',id_employee|max:50',
                    'position' => 'sometimes|required|string|in:Manager,Supervisor,Cashier,Stock,Sales,Other',
                    'salary' => 'sometimes|required|numeric|min:0|max:999999.99',
                    'status' => 'sometimes|required|string|in:Active,Inactive,On Leave',
                    'hire_date' => 'sometimes|required|date|date_format:Y-m-d',
                    'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:hire_date',
                    'fk_id_user' => 'nullable|integer|exists:users,id_user',
                    'fk_id_store' => 'sometimes|required|integer|exists:stores,id_store',
                    'notes' => 'nullable|string|max:1000',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            'full_name.required' => 'El nombre completo del empleado es obligatorio.',
            'full_name.string' => 'El nombre debe ser una cadena de texto.',
            'full_name.max' => 'El nombre no debe superar 255 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no debe superar 20 caracteres.',
            
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento no es válido.',
            
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'El número de documento ya está registrado.',
            'document_number.max' => 'El número de documento no debe superar 50 caracteres.',
            
            'position.required' => 'La posición del empleado es obligatoria.',
            'position.in' => 'La posición seleccionada no es válida.',
            
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.min' => 'El salario no puede ser negativo.',
            'salary.max' => 'El salario no debe superar 999,999.99.',
            
            'status.required' => 'El estado del empleado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            
            'hire_date.required' => 'La fecha de contratación es obligatoria.',
            'hire_date.date' => 'La fecha debe ser una fecha válida.',
            'hire_date.date_format' => 'El formato de la fecha debe ser YYYY-MM-DD.',
            
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.date_format' => 'El formato de la fecha debe ser YYYY-MM-DD.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de contratación.',
            
            'fk_id_user.integer' => 'El usuario debe ser un valor válido.',
            'fk_id_user.exists' => 'El usuario seleccionado no existe.',
            
            'fk_id_store.required' => 'La tienda es obligatoria.',
            'fk_id_store.integer' => 'La tienda debe ser un valor válido.',
            'fk_id_store.exists' => 'La tienda seleccionada no existe.',
            
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'notes.max' => 'Las notas no deben superar 1000 caracteres.',
        ];
    }
}
