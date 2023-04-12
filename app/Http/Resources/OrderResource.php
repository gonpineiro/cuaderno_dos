<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

        $data_type = $request->query('data_type');
        if (!$data_type) {
            $array['user'] = $this->user;
            $array['client'] = $this->client;
            $array['type'] = $this->type;
            $array['percentages'] = $this->getPercentages();
            $detalle = parent::toArray($this->detail);

            /* online */
            if ($this->type->value == 'online') {
                $array['orders_products'] = $this->online($this->details);
            }

            /* Pedido */
            if ($this->type->value == 'pedido') {
                $array['orders_products'] = $this->pedido($request);
            }

            /* Siniestro */
            if ($this->type->value == 'siniestro') {
                $array['orders_products'] = $this->siniestro($request);
            }
        }

        if ($data_type && $data_type == 'table') {
            unset($array['description']);
            $array['user'] = $this->user->name;
            $array['client'] = $this->client->name;
            $array['type'] = $this->type->value;
        }

        return $array;
    }

    private function online($request)
    {
        return OrderProduct::collection($this->detail);
    }

    private function pedido($request)
    {
        return OrderProduct::collection($this->detail);
    }

    private function siniestro($request)
    {
        return OrderProduct::collection($this->detail);
    }
}
