<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dni',
        'name',
        'phone',
        'email',
        'city',
        'adress',
        'cuit',
        'is_company',
    ];

    protected $dates = ['deleted_at'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
