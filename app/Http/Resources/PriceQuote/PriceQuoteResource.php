<?php

namespace App\Http\Resources\PriceQuote;

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
        /* Solicitamos todos los datos del objeto - Ideal si solo traemos elementos limitados */
        if (!$data_type) {
            $array['user'] = $this->user->toArray();
            $array['client'] = $this->client->toArray();

            $array['price_quotes_products'] = PriceQuoteProductResource::collection($this->detail);
        }

        if ($data_type && $data_type == 'table') {
            unset($array['description']);
            $array['user'] = $this->user->name;
            $array['client'] = $this->client;
        }

        return $array;
    }
}
