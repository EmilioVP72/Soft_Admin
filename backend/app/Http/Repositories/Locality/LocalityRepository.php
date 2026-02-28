<?php

namespace App\Http\Repositories\Locality;

use App\Models\Locality;

class LocalityRepository
{
    protected $locality;

    public function __construct(Locality $locality)
    {
        $this->locality = $locality;
    }

    public function all()
    {
        return $this->locality->get();
    }

    public function find($id)
    {
        return $this->locality->find($id);
    }

    public function create(array $data)
    {
        return $this->locality->create($data);
    }

    public function update($id, array $data)
    {
        $locality = $this->find($id);
        $locality?->update($data);
        return $locality;
    }

    public function delete($id)
    {
        $locality = $this->find($id);
        return $locality?->delete();
    }
}
