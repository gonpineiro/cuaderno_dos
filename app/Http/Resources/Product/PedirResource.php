<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Order\OrderResource;
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
            'code' => $this->product->code //
        ];

        $array['client'] = [
            'dni' => $this->order->client->dni ? $this->order->client->dni : $this->order->client->cuit,
            'name' => $this->order->client->name
        ];

        $array['order'] = [
            'id' => $this->order->id,
            'type' => $this->order->type->value,
            'created_at' => $this->order->created_at,
            'estimated_date' => $this->order->estimated_date
        ];

        return $array;
    }
}
