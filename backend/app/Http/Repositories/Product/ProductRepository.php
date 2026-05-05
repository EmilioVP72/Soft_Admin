<?php

namespace App\Http\Repositories\Product;

use App\Models\Product;

class ProductRepository
{
    public function getAll()
    {
        return Product::with(['supplier'])->get();
    }

    public function find($id)
    {
        return Product::with(['supplier', 'inventories'])->find($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product)
    {
        $product->delete();
    }
}
