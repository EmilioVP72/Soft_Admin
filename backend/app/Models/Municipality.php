<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $primaryKey = 'id_municipality';
    public $timestamps = true;

    protected $fillable = [
        'municipality',
        'fk1_id_state'
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'fk1_id_state', 'id_state');
    }

    public function localities()
    {
        return $this->hasMany(Locality::class, 'fk1_id_municipality', 'id_municipality');
    }
}
