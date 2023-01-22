<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = [
            "id" => $this->id,
            "name" => $this->name,
        ];

        if ($request->query('productos') == "true") {
            $array['productos'] = $this->products;
        }

        return $array;
    }
}
