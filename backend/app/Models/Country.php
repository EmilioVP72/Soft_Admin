<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id_country';
    protected $fillable = [
        'country'
        ];

    public function states() {
        return $this->hasMany(State::class, 'fk1_id_state');
    }
}
