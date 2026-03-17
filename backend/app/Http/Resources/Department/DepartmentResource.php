<?php

namespace App\Http\Resources\Department;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_department' => $this->id_department,
            'department' => $this->department,
            'description' => $this->description,
            'fk1_id_general_dep' => $this->fk1_id_general_dep,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'general_dep' => $this->whenLoaded('generalDep'),
        ];
    }
}
