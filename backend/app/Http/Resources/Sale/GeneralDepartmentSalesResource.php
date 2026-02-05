<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralDepartmentSalesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_general_dep' => $this['id_general_dep'] ?? $this->id_general_dep,
            'general_department' => $this['g_departament'] ?? $this->g_departament,
            'total_sales' => (float) ($this['total_sales'] ?? $this->total_sales),
            'total_transactions' => (int) ($this['total_transactions'] ?? $this->total_transactions),
            'total_quantity' => (float) ($this['total_quantity'] ?? $this->total_quantity),
        ];
    }
}
