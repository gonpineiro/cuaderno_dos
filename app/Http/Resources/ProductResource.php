<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
        $array['provider'] = $this->provider ? $this->provider->name : null;
        $array['brand'] = $this->brand->value;

        if ($request->query('ordenes') == "true") {
            $array['ordenes'] = $this->orders;
        }

        return $array;
    }
}