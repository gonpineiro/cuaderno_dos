<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    protected $customParam;

    public function __construct($resource, $customParam = null)
    {
        parent::__construct($resource);
        $this->customParam = $customParam;
    }

    public function toArray($request)
    {
        $array = parent::toArray($request);

        switch ((string) $this->customParam) {
            case 'complete':
                $array = $this->complete($array);
                break;
            default:
                $array = $this->default($array);
                break;
        }
        $array['estado_general'] = $this->getGeneralState();

        return $array;
    }

    private function complete($array)
    {
        $array['user'] = $this->user->toArray();

        $this->client->city;
        $array['client'] = $this->client;

        $array['type'] = $this->type->toArray();
        $array['percentages'] = $this->getPercentages();

        /* online */
        if ($this->type->value == 'online') {
            $array['orders_products'] = OrderProductResource::collection($this->detail);
        }

        /* Pedido */
        if ($this->type->value == 'pedido') {
            $array['orders_products'] = $this->pedido();
        }

        /* Siniestro */
        if ($this->type->value == 'siniestro') {
            $array['orders_products'] = $this->siniestro();
        }

        return $array;
    }

    private function default($array)
    {
        unset($array['description']);
        $array['user'] = $this->user->name;
        $array['client'] = $this->client;
        $array['type'] = $this->type->value;

        return $array;
    }

    private function online(/* $request */)
    {
        return OrderProductResource::collection($this->detail);
    }

    private function pedido(/* $request */)
    {
        return OrderProductResource::collection($this->detail);
    }

    private function siniestro(/* $request */)
    {
        return OrderProductResource::collection($this->detail);
    }
}
