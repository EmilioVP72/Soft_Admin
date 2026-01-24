<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $primaryKey = 'id_store';

    protected $fillable = [
        'store',
        'colony',
        'street',
        'exterior_number',
        'interior_number',
    ];
    public function locality() {
        return $this->belongsTo(Locality::class, 'fkl_id_localidad');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_stores', 'fk2_id_store', 'fk1_id_user')
                    ->withPivot('salary');
    }
}
