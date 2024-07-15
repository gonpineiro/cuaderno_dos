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
        'cuotas',
        'decimals',
        'show',
    ];

    public $timestamps = false;

    protected $casts = [
        'show' => 'boolean',
        'value' => 'double',
    ];
}
