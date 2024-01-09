<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'brand_id',
        'name'
    ];

    protected $hidden = [
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
