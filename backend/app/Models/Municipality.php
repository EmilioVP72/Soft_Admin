<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $primaryKey = 'id_municipality';
    protected $fillable = [
        'municipality',
        'fkl_id_state'
    ];

    public function state() {
        return $this->belongsTo(State::class, 'fkl_id_state');
    }

    public function localities() {
        return $this->hasMany(Locality::class, 'fkl_id_municipality');
    }
}
