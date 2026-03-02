<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $table = 'localities';
    protected $primaryKey = 'id_locality';
    public $timestamps = true;

    protected $fillable = [
        'locality',
        'fk1_id_municipality'
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'fk1_id_municipality', 'id_municipality');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'fk1_id_locality', 'id_locality');
    }
}
