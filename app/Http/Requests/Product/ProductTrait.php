<?php

namespace App\Http\Requests\Product;

use App\Models\Table;

trait ProductTrait
{
    /** Verifica que el id de la marca corresponda realmente a una marca en la tabla 'table' */
    private function authBrand()
    {
        try {
            $table_id = $this->all()['brand_id'];
            $table = Table::where('id', $table_id)->first();

            if ($table->name == 'brand') {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
