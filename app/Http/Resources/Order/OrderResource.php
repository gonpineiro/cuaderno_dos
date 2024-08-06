<?php

namespace App\Http\Resources\Order;

use App\Models\Table;
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
        $array['estado_shipment'] = $this->shipment ? $this->shipment->getGeneralState() : null;

        return $array;
    }

    private function complete($array)
    {
        $array['user'] = $this->user;

        $this->client->city;
        $array['client'] = $this->client;

        $this->payment_method && $array['payment_method'] = $this->payment_method;

        $array['type'] = $this->type;

        $array['init_state'] = null;
        if ($this->type->value == 'online') {
            $array['init_state'] = Table::where('name', 'order_online_state')->where('value', 'pendiente')->first();
        } elseif ($this->type->value == 'cliente') {
            $array['init_state'] = Table::where('name', 'order_cliente_state')->where('value', 'pendiente')->first();
        } elseif ($this->type->value == 'siniestro') {
            $array['init_state'] = Table::where('name', 'order_siniestro_state')->where('value', 'incompleto')->first();
        }

        //$array['percentages'] = $this->getPercentages();

        $array['price_quote'] = $this->price_quote;
        $array['shipment'] = $this->shipment;

        $array['detail'] = OrderProductResource::collection($this->detail);

        return $array;
    }

    private function default($array)
    {
        unset($array['observation']);
        unset($array['detail']);
        $array['user'] = $this->user->name;
        $array['client'] = $this->client;
        $array['type'] = $this->type->value;
        $this->payment_method && $array['payment_method'] = $this->payment_method->description;
        $array['vehiculo'] = $this->vehiculo;

        return $array;
    }
}
