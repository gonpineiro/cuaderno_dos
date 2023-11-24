<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coeficiente extends Model
{
    protected $table = 'coeficientes';

    protected $fillable = [
        'description',
        'value',
        'cuotas',
        'decimals',
        'show',
    ];

    public $timestamps = false;
}
