<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_product' => $this->id_product,
            'product' => $this->product,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'purchase_price' => (float) $this->purchase_price,
            'sale_price' => (float) $this->sale_price,
            'unit' => $this->unit,
            'is_active' => (bool) $this->is_active,
            'fk1_id_supplier' => $this->fk1_id_supplier,
            'supplier' => $this->whenLoaded('supplier'),
            'total_physical_stock' => $this->total_physical_stock,
            'pending_verification' => $this->pending_verification,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
