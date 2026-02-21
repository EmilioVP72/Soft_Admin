<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_employee' => $this->id_employee,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'position' => $this->position,
            'salary' => (float) $this->salary,
            'status' => $this->status,
            'status_label' => $this->status_label_attribute,
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            
            // Relaciones
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id_user' => $this->user->id_user,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            
            'store' => $this->whenLoaded('store', function () {
                return [
                    'id_store' => $this->store->id_store,
                    'store' => $this->store->store,
                    'colony' => $this->store->colony,
                    'street' => $this->store->street,
                ];
            }),
            
            // Para compatibilidad
            'id' => $this->id_employee,
            'name' => $this->full_name,
        ];
    }
}
