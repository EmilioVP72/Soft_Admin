<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $table = 'product_inventories';
    protected $primaryKey = 'id_product_inventory';
    public $timestamps = true;

    protected $fillable = [
        'fk1_id_product',
        'ticket_quantity',
        'physical_quantity',
        'difference',
        'status',
        'notes',
        'ticket_date',
        'verified_at',
    ];

    protected $casts = [
        'ticket_quantity'   => 'decimal:2',
        'physical_quantity' => 'decimal:2',
        'difference'        => 'decimal:2',
        'ticket_date'       => 'datetime',
        'verified_at'       => 'datetime',
    ];

    // Relaciones
    public function product()
    {
        return $this->belongsTo(Product::class, 'fk1_id_product', 'id_product');
    }

    // Recalcula la diferencia y el estado automáticamente al verificar físicamente
    public function verify(float $physicalQuantity, ?string $notes = null): void
    {
        $diff = $physicalQuantity - (float) $this->ticket_quantity;

        $this->update([
            'physical_quantity' => $physicalQuantity,
            'difference'        => $diff,
            'status'            => $diff === 0.0 ? 'verified' : 'discrepancy',
            'verified_at'       => now(),
            'notes'             => $notes ?? $this->notes,
        ]);
    }
}
