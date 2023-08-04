<?php

namespace App\Http\Resources\PriceQuote;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceQuoteResource extends JsonResource
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

        return $array;
    }

    private function complete($array)
    {
        $array['type_price'] = $this->type_price;
        $array['information_source'] = $this->information_source;
        $array['user'] = $this->user;

        $array['to_asign'] = $this->to_asign;

        $this->client->city;
        $array['client'] = $this->client;

        $array['observation'] = $this->observation;
        if ($this->order) {
            $this->order->type;
            $array['order'] = $this->order;
            $array['order']['estado_general'] = $this->order->getGeneralState();
        } else {
            $array['order'] = null;
        }

        $array['detail'] = PriceQuoteProductResource::collection($this->detail);
        $array['state'] = $this->formatState($this->order);

        return $array;
    }

    private function default($array)
    {
        $array['user'] = $this->user->name;
        $array['client']['name'] = $this->client->name;
        $array['client']['phone'] = $this->client->phone;
        $array['state'] = $this->formatState($this->order);

        return $array;
    }

    private function formatState($order)
    {
        if ($order) {
            $type = $order->type->toArray();

            unset($type['id']);
            unset($type["background_color"]);
            unset($type["color"]);

            if ($type['value'] === 'online') {
                $type['string'] = 'PEDIDO';
                $type['className'] = 'pedido';
            } else if ($type['value'] === 'cliente') {
                $type['string'] = 'PEDIDO';
                $type['className'] = 'pedido';
            } else if ($type['value'] === 'siniestro') {
                $type['string'] =  'PEDIDO SINIESTRO';
                $type['className'] = 'siniestro';
            }
        } else {
            $type = [];
            $type['value'] = 'pendiente';
            $type['string'] = 'PENDIENTE';
            $type['className'] = '';

            $fechaActual = \Carbon\Carbon::now();
            $diferenciaDias = $this->created_at->diffInDays($fechaActual);

            if ($diferenciaDias >= 7) {
                $type['className'] = 'vencido';
            }
        }

        return $type;
    }
}
