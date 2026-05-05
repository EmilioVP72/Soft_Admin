<?php

namespace App\Http\Resources\ProductInventory;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductInventoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_product_inventory' => $this->id_product_inventory,
            'fk1_id_product' => $this->fk1_id_product,
            'product' => $this->whenLoaded('product'),
            'ticket_quantity' => (float) $this->ticket_quantity,
            'physical_quantity' => $this->physical_quantity !== null ? (float) $this->physical_quantity : null,
            'difference' => $this->difference !== null ? (float) $this->difference : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'ticket_date' => $this->ticket_date,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
