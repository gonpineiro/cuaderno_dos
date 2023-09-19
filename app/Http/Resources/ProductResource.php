<?php

namespace App\Http\Resources;

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
        $array['provider'] = $this->provider ? $this->provider->name : null;
        $array['brand'] = $this->brand ? $this->brand->value : null;
        $array['ubication'] = $this->ubication;
        $array['state'] = $this->state ? $this->state->value : null;

        if ($request->query('ordenes') == "true") {
            $array['ordenes'] = count($this->orders) > 0 ? $this->orders : null;
        }

        return $array;
    }
}
