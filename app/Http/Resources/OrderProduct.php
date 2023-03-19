<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->product) {
            $array['product'] =  $this->product;
        }

        if ($this->otherProduct) {
            $array['product'] =  $this->otherProduct;
        }
        $array['state'] =  $this->state->value;

        return $array;
    }
}
