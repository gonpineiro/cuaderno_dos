<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceQuoteResource extends JsonResource
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

        /* Parametros del GET */
        $data_type = $request->query('data_type');

        if ($data_type && $data_type == 'table') {
            unset($array['description']);
            $array['user'] = $this->user->name;
            $array['client'] = $this->client;
        }

        return $array;
    }
}
