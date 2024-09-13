<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        $array['provider'] = (isset($this->provider) && $this->provider) ? $this->provider : null;
        $array['brand'] = $this->brand ? $this->brand->name : null;
        $array['ubication'] = $this->ubication;
        $array['description'] = $this->description;

        if ($this->is_special) {
            $array['state'] = 'is_special';
        } else if (!$this->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $this->state ? $this->state->value : null;
        }

        return $array;
    }

    public static function complete(Product $product)
    {
        $product->providers;
        $product->brand;
        $product->state;
        /* $product->orders;
        $product->price_quotes; */

        $product['ubication'] =  $product->ubication;

        /*  $array = parent::toArray($product);
        $array['provider'] = (isset($this->provider) && $this->provider) ? $this->provider->name : null;
        $array['brand'] = $this->brand ? $this->brand->name : null;
        $array['ubication'] = $this->ubication;
        $array['description'] = $this->description;

        if ($this->is_special) {
            $array['state'] = 'is_special';
        } else if (!$this->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $this->state ? $this->state->value : null;
        } */

        /* $array['cantidad_cotizaciones'] = $this->price_quotes->count(); */

        return $product;
    }

    public static function order(Product $product, $order_product)
    {
        $array = $product->toArray();
        $array['order_product_id'] = $order_product->id;
        $array['provider'] = (isset($product->provider) && $product->provider) ? $product->provider->name : null;
        $array['brand'] = $product->brand ? $product->brand->name : null;
        $array['ubication'] = $product->ubication;
        $array['description'] = $product->description;

        if ($product->is_special) {
            $array['state'] = 'is_special';
        } else if (!$product->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $product->state ? $product->state->value : null;
        }

        $array['order_state'] = $order_product->order->getGeneralState();
        return $array;
    }
}
