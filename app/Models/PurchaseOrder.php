<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model{

    protected $fillable = [
        'observation',
        'provider_id',
        'state_id',
    ];

    protected $hidden = [
        'provider_id',
        'state_id',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTrashed();
    }

    public function state()
    {
        return $this->belongsTo(Table::class);
    }

    public function detail()
    {
        return $this->hasMany(PurchaseOrderProduct::class);
    }
}
