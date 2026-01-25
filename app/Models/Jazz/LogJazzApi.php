<?php

namespace App\Models\Jazz;

use Illuminate\Database\Eloquent\Model;

class LogJazzApi extends Model
{
    protected $table = 'log_jazz_api';

    protected $fillable = [
        'endpoint',
        'metod',
        'user_id',
        'request',
        'response',
        'time_ms',
        'error',
    ];

    protected $casts = [
        'request'  => 'array',
        'response' => 'array',
        'error'    => 'array',
    ];
}
