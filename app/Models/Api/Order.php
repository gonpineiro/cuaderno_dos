<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
