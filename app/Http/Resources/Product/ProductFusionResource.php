<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFusionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$array = parent::toArray($request);
        $array = [
            /* Primitivos */
            'code' => $this->code,
            'idProducto' => $this->idProducto,
            'provider_code' => $this->provider_code,
            'factory_code' => $this->factory_code,
            'equivalence' => $this->equivalence,
            'description' => $this->description,
            'model' => $this->model,
            'engine' => $this->engine,
            'observation' => $this->observation,
            'observation' => $this->observation,
            'ubication' => $this->ubication,

            /* No primitivos */
            'providers' => $this->product_providers,
            'provider' => $this->provider ? $this->provider : null,
            'brand' => $this->brand ? $this->brand : null,
            'product_brand' => $this->product_brand ? $this->product_brand : null,


        ];

        if ($this->is_special) {
            $array['state'] = 'is_special';
        } else if (!$this->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $this->state ? $this->state->value : null;
        }

        $array['jazz'] = $this->jazz;

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

    public static function order(Product $product, $order_product, $audit = true, $precalculatedState = null)
    {
        $array = $product->toArray();
        $array['order_product_id'] = $order_product->id;
        $array['provider'] = $product->provider->name ?? null;
        $array['brand'] = $product->brand->name ?? null;
        $array['ubication'] = $product->ubication;
        $array['description'] = $product->description;
        $array['activities'] = $audit ?  AuditResource::collection($product->activities) : null;

        if ($product->is_special) {
            $array['state'] = 'is_special';
        } else if (!$product->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $product->state->value ?? null;
        }

        $array['order_state'] = $precalculatedState;

        return $array;
    }
}
