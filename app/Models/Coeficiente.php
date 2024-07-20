<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Coeficiente extends Model
{
    use LogsActivity;
    protected $table = 'coeficientes';

    protected $fillable = [
        'description',
        'value',
        'coeficiente',
        'cuotas',
        'decimals',
        'position',
        'show',
    ];

    public $timestamps = false;

    protected $casts = [
        'show' => 'boolean',
        'value' => 'double',
        'coeficiente' => 'double',
    ];
}
