<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComboProduct extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'combo_products';

    protected $fillable = [
        'combo_id',
        'product_id',
    ];

    protected $hidden = [
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

}
