<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCotizacionesResource extends JsonResource
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
        $array['description'] = $this->description;
        $array['activities'] = AuditResource::collection($this->activities);

        if ($this->is_special) {
            $array['state'] = 'is_special';
        } else if (!$this->ubication) {
            $array['state'] = 'is_simple';
        } else {
            $array['state'] = $this->state ? $this->state->value : null;
        }

        $array['cantidad_cotizaciones'] = count($this->price_quotes) > 0 ? count($this->price_quotes) : 0;

        return $array;
    }
}
