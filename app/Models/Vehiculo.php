<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use LogsActivity;
    protected $table = 'vehiculos';

    protected $fillable = [
        'brand_id',
        'name'
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
