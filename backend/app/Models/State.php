<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    protected $primaryKey = 'id_state';
    public $timestamps = true;

    protected $fillable = [
        'state',
        'fk1_id_country'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'fk1_id_country', 'id_country');
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'fk1_id_state', 'id_state');
    }
}
