<?php

namespace App\Http\Resources\PurchaseOrder;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEvaluarPedirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'code' => $this->code,
            'ubication' => $this->ubication,
            'description' => $this->description,
            'stock' => $this->jazz->stock,
            'stock_min' => (float)$this->jazz->stock_min,
            'total_to_ask' => (int)$this->total_to_ask,
            /* 'stock_max' => (float)$this->jazz->stock_max, */
            'punto_pedido' => (float)$this->jazz->punto_pedido,
            'provider_id' => (float)$this->provider_id,
        ];

        return $data;
    }
}
