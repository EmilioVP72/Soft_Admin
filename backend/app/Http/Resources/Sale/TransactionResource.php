<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_transaction' => $this->id_transaction,
            'id_store' => $this->fk1_id_store,
            'store_name' => $this->store?->store ?? null,
            'user_name' => $this->user?->name ?? null,
            'total_amount' => (float) $this->total_amount,
            'transaction_type' => $this->transaction_type,
            'notes' => $this->notes,
            'transaction_date' => $this->transaction_date?->format('Y-m-d H:i:s'),
            'details' => $this->details->map(function ($detail) {
                return [
                    'id_transaction_detail' => $detail->id_transaction_detail,
                    'department' => $detail->department?->department,
                    'quantity' => (float) $detail->quantity,
                    'unit_price' => (float) $detail->unit_price,
                    'subtotal' => (float) $detail->subtotal,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
