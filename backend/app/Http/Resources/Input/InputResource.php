<?php

namespace App\Http\Resources\Input;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InputResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_transaction' => $this->id_transaction,
            'store' => [
                'id_store' => $this->store->id_store ?? null,
                'store' => $this->store->store ?? null,
            ],
            'user' => [
                'id_user' => $this->user->id_user ?? null,
                'name' => $this->user->name ?? null,
            ],
            'payment' => [
                'id_payment' => $this->payment->id_payment ?? null,
                'payment' => $this->payment->payment ?? null,
            ],
            'total_amount' => $this->total_amount,
            'transaction_type' => $this->transaction_type,
            'notes' => $this->notes,
            'transaction_date' => $this->transaction_date,
            'details' => InputDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
