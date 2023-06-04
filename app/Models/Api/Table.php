<?php

namespace App\Models\Api;

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
