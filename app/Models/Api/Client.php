<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
