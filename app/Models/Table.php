<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $hidden = [
        'enable',
        'description',
        'created_at',
        'updated_at',
    ];
}
