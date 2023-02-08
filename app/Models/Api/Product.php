<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_id',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function brand()
    {
        return $this->belongsTo(Table::class);
    }
}
