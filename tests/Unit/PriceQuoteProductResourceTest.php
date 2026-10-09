<?php

namespace Tests\Unit;

use App\Http\Resources\PriceQuote\PriceQuoteProductResource;
use App\Models\PriceQuoteProduct;
use App\Models\Product;
use App\Models\ProductJazz;
use Illuminate\Http\Request;
use Tests\TestCase;

class PriceQuoteProductResourceTest extends TestCase
{
    public function test_it_only_uses_the_current_product_price_when_requested(): void
    {
        $product = new Product([
            'id' => 10,
            'code' => 'P-10',
            'description' => 'Producto',
            'is_special' => true,
        ]);
        $product->setRelation('jazz', new ProductJazz(['precio_lista_2' => 250]));
        $product->setRelation('product_providers', collect());
        $product->setRelation('provider', null);
        $product->setRelation('brand', null);
        $product->setRelation('product_brand', null);
        $product->setRelation('activities', collect());

        $detail = new PriceQuoteProduct([
            'amount' => 2,
            'unit_price' => 100,
        ]);
        $detail->setRelation('product', $product);
        $detail->setRelation('state', null);
        $detail->setRelation('provider', null);

        $historical = (new PriceQuoteProductResource($detail))
            ->resolve(Request::create('/'));
        $updated = (new PriceQuoteProductResource($detail))
            ->resolve(Request::create('/?update_prices=1'));

        $this->assertSame(100, $historical['unit_price']);
        $this->assertSame(250, $updated['unit_price']);
    }
}
