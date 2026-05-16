<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id_product';
    public $timestamps = true;

    protected $fillable = [
        'product',
        'barcode',
        'description',
        'purchase_price',
        'sale_price',
        'unit',
        'is_active',
        'fk1_id_supplier',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    // Relaciones
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'fk1_id_supplier', 'id_supplier');
    }

    public function inventories()
    {
        return $this->hasMany(ProductInventory::class, 'fk1_id_product', 'id_product');
    }

    // Último registro de inventario
    public function latestInventory()
    {
        return $this->hasOne(ProductInventory::class, 'fk1_id_product', 'id_product')
                    ->latestOfMany('ticket_date');
    }

    // Stock total verificado físicamente (suma de physical_quantity en registros verified/discrepancy)
    public function getTotalPhysicalStockAttribute()
    {
        return $this->inventories()
                    ->whereIn('status', ['verified', 'discrepancy'])
                    ->whereNotNull('physical_quantity')
                    ->sum('physical_quantity');
    }

    // Entradas pendientes de verificación
    public function getPendingVerificationAttribute()
    {
        return $this->inventories()->where('status', 'pending')->count();
    }
}
