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

        /* Parametros del GET */
        $data_type = $request->query('data_type');

        /* Solicitamos todos los datos del objeto - Ideal si solo traemos elementos limitados */
        if (!$data_type) {
            $array['user'] = $this->user->toArray();
            $array['client'] = $this->client->toArray();
            $array['type'] = $this->type->toArray();
            $array['percentages'] = $this->getPercentages();

            /* online */
            if ($this->type->value == 'online') {
                $array['orders_products'] = $this->online();
            }

            /* Pedido */
            if ($this->type->value == 'pedido') {
                $array['orders_products'] = $this->pedido();
            }

            /* Siniestro */
            if ($this->type->value == 'siniestro') {
                $array['orders_products'] = $this->siniestro();
            }
        }

        /* Informacion acotada para las tablas */
        if ($data_type && $data_type == 'table') {
            unset($array['description']);
            $array['user'] = $this->user->name;
            $array['client'] = $this->client;
            $array['type'] = $this->type->value;
        }
        $array['estado_general'] = $this->getGeneralState();

        return $array;
    }

    private function online(/* $request */)
    {
        return OrderProduct::collection($this->detail);
    }

    private function pedido(/* $request */)
    {
        return OrderProduct::collection($this->detail);
    }

    private function siniestro(/* $request */)
    {
        return OrderProduct::collection($this->detail);
    }
}
