<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'payment' => 'required|string|max:255',
            'description' => 'required|string',
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
            'payment.required' => 'El nombre del método de pago es obligatorio',
            'payment.string' => 'El nombre del método de pago debe ser texto',
            'payment.max' => 'El nombre del método de pago no debe exceder los 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'La descripción debe ser texto',
        ];
    }
}
