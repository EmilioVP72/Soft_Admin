<?php

namespace App\Http\Repositories\Store;

use App\Models\Store;

class StoreRepository
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function all()
    {
        return $this->store->get();
    }

    public function find($id)
    {
        return $this->store->find($id);
    }

    public function create(array $data)
    {
        return $this->store->create($data);
    }

    public function update($id, array $data)
    {
        $store = $this->find($id);
        $store?->update($data);
        return $store;
    }

    public function delete($id)
    {
        $store = $this->find($id);
        return $store?->delete();
    }
}
