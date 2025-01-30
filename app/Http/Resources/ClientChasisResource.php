<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientChasisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'chasis' => $this->chasis,
            'brand' => $this->vehiculo->brand->name,
            'vehiculo' => $this->vehiculo->name,
            'year' => $this->year,
            'client' => $this->client->name,
        ];
    }
}
