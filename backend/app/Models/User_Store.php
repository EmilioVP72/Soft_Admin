<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Store extends Model
{
    protected $table = 'user_stores';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'salary',
        'fk1_id_user',
        'fk2_id_store'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk1_id_user', 'id_user');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'fk2_id_store', 'id_store');
    }
}
