<?php

namespace App\Http\Resources\GeneralDepartment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralDepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_general_dep' => $this->id_general_dep,
            'g_departament' => $this->g_departament,
            'g_descripcion' => $this->g_descripcion,
            'fkl_id_tienda' => $this->fkl_id_tienda,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'store' => $this->whenLoaded('store'),
        ];
    }
}
