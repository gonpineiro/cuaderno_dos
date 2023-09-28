<?php

namespace App\Http\Resources\Product;

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
        $array['provider'] = (isset($this->provider) && $this->provider) ? $this->provider->name : null;
        $array['brand'] = $this->brand ? $this->brand->name : null;
        $array['ubication'] = $this->ubication;

        if ($this->is_special) {
            $array['state'] = 'is_special';
        } else {
            $array['state'] = $this->state ? $this->state->value : null;
        }

        if ($request->query('ordenes') == "true") {
            $array['ordenes'] = count($this->orders) > 0 ? $this->orders : null;
        }

        return $array;
    }
}
