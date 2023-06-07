<?php

namespace App\Http\Resources\PriceQuote;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ProductResource;

class PriceQuoteProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);

        $array['product'] =  new ProductResource($this->otherProduct);

        $array['state'] =  $this->state;

        return $array;
    }
}
