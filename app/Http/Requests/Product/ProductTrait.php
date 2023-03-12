<?php

namespace App\Http\Requests\Product;

use App\Models\Api\Table;

trait ProductTrait
{
    /** Verifica que el id de la marca corresponda realmente a una marca en la tabla 'table' */
    private function authBrand()
    {
        $table_id = $this->all()['brand_id'];
        $table = Table::where('id', $table_id)->first();

        if ($table->name == 'brand') {
            return true;
        }

        return false;
    }
}
