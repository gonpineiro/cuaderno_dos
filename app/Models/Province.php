<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';

    protected $fillable = [
        'name',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
