<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentSalesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_department' => $this['id_department'] ?? $this->id_department,
            'department' => $this['department'] ?? $this->department,
            'general_department' => $this['general_department'] ?? $this->general_department,
            'total_sales' => (float) ($this['total_sales'] ?? $this->total_sales),
            'total_transactions' => (int) ($this['total_transactions'] ?? $this->total_transactions),
            'total_quantity' => (float) ($this['total_quantity'] ?? $this->total_quantity),
            'id_store' => $this['id_store'] ?? null,
            'store' => $this['store'] ?? null,
        ];
    }
}
