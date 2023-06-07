<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOther extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'provider_id',
        'brand_id',
    ];

    protected $hidden = [
        'pivot',
        'provider_id',
        'brand_id',
        'created_at',
        'updated_at'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function brand()
    {
        return $this->belongsTo(Table::class);
    }
}
