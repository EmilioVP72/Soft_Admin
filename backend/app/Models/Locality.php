<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $table = 'localities';
    protected $primaryKey = 'id_locality';
    protected $fillable = [
        'locality', 
        'fkl_id_municipality'
    ];

    public function municipality() {
        return $this->belongsTo(Municipality::class, 'fkl_id_municipality');
    }

    public function stores() {
        return $this->hasMany(Store::class, 'fkl_id_locality');
    }
}
