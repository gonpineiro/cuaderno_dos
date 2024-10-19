<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class FueraCatalogoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array['code'] = $this->code;
        $array['provider_code'] = $this->provider_code;
        $array['factory_code'] = $this->factory_code;
        $array['equivalence'] = $this->equivalence;
        $array['description'] = $this->description;
        $array['model'] = $this->model;
        $array['engine'] = $this->engine;
        $array['brand'] = $this->brand ? $this->brand->name : null;
        $array['provider'] = (isset($this->provider) && $this->provider) ? $this->provider->name : null;

        return $array;
    }
}
