<?php

namespace App\Http\Resources\Supplier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_supplier_payment' => $this->id_supplier_payment,
            'amount_paid' => $this->amount_paid,
            'payment_date' => $this->payment_date,
            'supplier' => [
                'id_supplier' => $this->supplier->id_supplier,
                'supplier' => $this->supplier->supplier,
            ],
            'department' => [
                'id_department' => $this->department->id_department,
                'department' => $this->department->department,
            ],
        ];
    }
}
