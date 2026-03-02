<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    protected $table = 'receipts';
    protected $primaryKey = 'id_receipt';
    public $timestamps = true;

    protected $fillable = [
        'receipt',
        'description'
    ];

    /**
     * Relación One to Many con Receipt_Type
     */
    public function receiptTypes()
    {
        return $this->hasMany(Receipt_Type::class, 'fk2_id_receipt', 'id_receipt');
    }
}
