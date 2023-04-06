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
        /* Producto dentro del catalogo */
        $array = parent::toArray($request);
        if ($this->product) {
            $array['product'] =  $this->product;
        }

        /* Producto que no se encuentra en el catalogo */
        if ($this->otherProduct) {
            $array['product'] =  $this->otherProduct;
        }
        $array['state'] =  $this->state->value;

        return $array;
    }
}
