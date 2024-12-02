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
        $array['providers'] = $this->product_providers;
        $array['provider'] = (isset($this->provider) && $this->provider) ? $this->provider->name : null;
        $array['brand'] = $this->brand ? $this->brand->name : null;
        $array['product_brand'] = $this->product_brand ? $this->product_brand->name : null;
        $array['ubication'] = $this->ubication;
        $array['description'] = $this->description;
        $array['activities'] = AuditResource::collection($this->activities);

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
        $array = $product->toArray();
        $array['providers'] = $product->product_providers;
        $array['provider'] = (isset($product->provider) && $product->provider) ? $product->provider->name : null;
        $array['brand'] = $product->brand ? $product->brand->name : null;
        $array['product_brand'] = $product->product_brand ? $product->product_brand->name : null;
        $array['ubication'] = $product->ubication;
        $array['description'] = $product->description;
        $array['activities'] = AuditResource::collection($product->activities);

        if ($product->is_special) {
            $array['state'] = 'is_special';
        } else if (!$product->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $product->state ? $product->state->value : null;
        }

        return $array;
    }

    public static function order(Product $product, $order_product)
    {
        $array = $product->toArray();
        $array['order_product_id'] = $order_product->id;
        $array['provider'] = (isset($product->provider) && $product->provider) ? $product->provider->name : null;
        $array['provider'] = (isset($product->provider) && $product->provider) ? $product->provider->name : null;
        $array['brand'] = $product->brand ? $product->brand->name : null;
        $array['ubication'] = $product->ubication;
        $array['description'] = $product->description;
        $array['activities'] = AuditResource::collection($product->activities);

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
