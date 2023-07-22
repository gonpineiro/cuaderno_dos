<?php

namespace App\Models;

use App\Models\{Table, Product, ProductOther};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuoteProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'price_quote_id',
        'state_id',
        'product_id',
        'amount',
        'unit_price',
        'quote',
        'description'
    ];

    protected $hidden = [
        'price_quote_id',
        'state_id',
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function price_quote()
    {
        return $this->belongsTo(PriceQuote::class);
    }
}
