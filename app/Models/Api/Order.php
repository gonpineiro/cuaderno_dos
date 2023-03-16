<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'detalle',
    ];

    protected $hidden = [
        'user_id',
        'updated_at',
        'pivot',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    public function detail()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function type()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getPercentages()
    {
        $array['pendiente'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $array['avisado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'avisado';
        });

        $array['cancelado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $array['rechazado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'rechazado';
        });

        $count = count($this->detail);

        foreach ($array as $key => $value) {
            $array[$key] = ($value * 100) / $count;
        }

        return $array;
    }
}
