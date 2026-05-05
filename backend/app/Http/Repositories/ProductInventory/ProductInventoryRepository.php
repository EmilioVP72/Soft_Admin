<?php

namespace App\Http\Repositories\ProductInventory;

use App\Models\ProductInventory;

class ProductInventoryRepository
{
    public function getAll()
    {
        return ProductInventory::with(['product'])->get();
    }

    public function find($id)
    {
        return ProductInventory::with(['product'])->find($id);
    }

    public function create(array $data)
    {
        return ProductInventory::create($data);
    }

    public function update(ProductInventory $inventory, array $data)
    {
        $inventory->update($data);
        return $inventory;
    }

    public function delete(ProductInventory $inventory)
    {
        $inventory->delete();
    }

    public function getByProduct($productId)
    {
        return ProductInventory::with(['product'])->where('fk1_id_product', $productId)->get();
    }
}
