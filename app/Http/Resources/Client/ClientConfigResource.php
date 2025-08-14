<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\BaseTableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientConfigResource extends JsonResource
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
            'type' => $this->type,
            'es_cuenta_corriente' => $this->es_cuenta_corriente,
            'type_price' => new BaseTableResource($this->type_price),
            'information_source' => new BaseTableResource($this->information_source),
        ];
    }
}
