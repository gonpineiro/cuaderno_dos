<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConfig extends Model
{
    protected $table = 'client_config';

    protected $fillable = [
        'client_id',
        'type',
        'information_source_id',
        'type_price_id',
        'es_cuenta_corriente',
    ];

    protected $hidden = [
        'client_id',
        'created_at',
        'updated_at',
        'type_price_id',
        'information_source_id'
    ];

    protected $append = ['type_price'];

    public function type_price()
    {
        return $this->belongsTo(Table::class, 'type_price_id');
    }

    public function information_source()
    {
        return $this->belongsTo(Table::class, 'information_source_id');
    }
}
