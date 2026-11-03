<?php

namespace Tests\Unit;

use App\Services\JazzServices\ProductService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    public function test_it_normalizes_only_products_without_ship(): void
    {
        DB::shouldReceive('affectingStatement')
            ->once()
            ->with(\Mockery::on(function ($sql) {
                return strpos($sql, 'JOIN product_jazz pj ON pj.id = p.idProducto AND pj.code = p.code') !== false
                    && strpos($sql, 'AND p.ship IS NULL') !== false
                    && strpos($sql, "p.ship = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1))") !== false;
            }))
            ->andReturn(1);

        $this->assertSame(1, ProductService::normalizeProductLocations());
    }
}
