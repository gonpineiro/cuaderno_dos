<?php

namespace App\Models;

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
        'city_id',
        'adress',
        'cuit',
        'is_insurance',
        'is_company',
    ];

    protected $hidden = [
        'city_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($cliente) {
            if ($cliente->orders()->count() > 0) {
                // Si el cliente tiene pedidos, lanza una excepción para evitar la eliminación
                throw new \Exception("No se puede eliminar el cliente porque tiene pedidos asociados.");
            }
        });
    }
}
