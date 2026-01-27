<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_store' => $this->id_store,
            'store' => $this->store,
            'colony' => $this->colony,
            'street' => $this->street,
            'exterior_number' => $this->exterior_number,
            'interior_number' => $this->interior_number,
            'reference' => $this->reference,
            'fk1_id_locality' => $this->fk1_id_locality,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id_store,
            'name' => $this->store,
        ];
    }
}
