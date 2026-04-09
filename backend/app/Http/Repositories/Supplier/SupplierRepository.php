<?php

namespace App\Http\Repositories\Supplier;

use App\Models\Supplier;
use App\Models\SupplierPayment;

class SupplierRepository
{
    public function getAll()
    {
        return Supplier::all();
    }

    public function find($id)
    {
        return Supplier::find($id);
    }

    public function create(array $data)
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data)
    {
        $supplier->update($data);
        return $supplier;
    }

    public function delete(Supplier $supplier)
    {
        $supplier->delete();
    }

    public function createPayment(array $data)
    {
        return SupplierPayment::create($data);
    }

    public function getAllPayments()
    {
        return SupplierPayment::with(['supplier', 'department'])->get();
    }

    public function getPaymentsBySupplier($supplierId)
    {
        return SupplierPayment::with(['supplier', 'department'])
            ->where('fk1_id_supplier', $supplierId)->get();
    }
}
