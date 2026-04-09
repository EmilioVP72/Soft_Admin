<?php

namespace App\Http\Resources\Input;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InputDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_transaction_detail' => $this->id_transaction_detail,
            'department' => [
                'id_department' => $this->department->id_department ?? null,
                'department' => $this->department->department ?? null,
            ],
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
        ];
    }
}
