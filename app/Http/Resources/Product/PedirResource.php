<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class PedirResource extends JsonResource
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

        $array['provider'] = $this->provider;

        $array['product'] = [
            'id' => $this->product->id,
            'code' => $this->product->code,
            'ubication' => $this->product->ubication,
            'description' => $this->product->description
        ];

        /* $array['client'] = [
            'dni' => $this->order->client->dni ? $this->order->client->dni : $this->order->client->cuit,
            'name' => $this->order->client->name
        ]; */

        $array['order'] = [
            'id' => $this->order_product ? $this->order_product->order->id : null,
            'type' => $this->order_product ? $this->order_product->order->type->value : null,
            'created_at' => $this->order_product ? $this->order_product->order->created_at : null,
            'estimated_date' => $this->order_product ? $this->order_product->order->estimated_date : null
        ];

        return $array;
    }
}
