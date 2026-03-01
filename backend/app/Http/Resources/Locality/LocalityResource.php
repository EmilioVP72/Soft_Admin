<?php

namespace App\Http\Resources\Locality;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_locality' => $this->id_locality,
            'locality' => $this->locality,
            'fk1_id_municipality' => $this->fk1_id_municipality,
            'municipality' => $this->whenLoaded('municipality', function() {
                return $this->municipality;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
