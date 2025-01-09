<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\{Table, Product};
use App\Models\Traits\LogsActivity;

class PriceQuoteProduct extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'price_quote_id',
        'state_id',
        'product_id',
        'provider_id',
        'amount',
        'unit_price',
        'description',
        'quote',
    ];

    protected static $logAttributes = [
        'price_quote_id',
        'state_id',
        'product_id',
        'provider_id',
        'amount',
        'description',
        'unit_price',
        'quote',
    ];

    protected $hidden = [
        'state_id',
        'provider_id',
        'product_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    protected $table = 'price_quote_product';

    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(Table::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function price_quote()
    {
        return $this->belongsTo(PriceQuote::class);
    }
}
