<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\ClientResource;
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
        //$array['estado_shipment'] = $this->shipment ? $this->shipment->getGeneralState() : null;

        return $array;
    }

    private function complete($array)
    {
        $array['type_price'] = $this->price_quote->type_price;
        $array['user'] = $this->user;

        $this->client->city;
        $array['client'] = $this->client;

        $this->payment_method && $array['payment_method'] = $this->payment_method;

        $array['type'] = $this->type;
        $array['user_complete'] = $this->user_complete;

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

        $array['activity'] = $this->activities;

        $array['estado_shipment'] = $this->shipment ? $this->shipment->getGeneralState() : null;
        return $array;
    }

    private function default($array)
    {
        unset($array['observation']);
        unset($array['detail']);
        $array['user'] = $this->user->name;
        //$array['client']['id'] = $this->client->id;
        $array['client']['name'] = $this->client->name;
        $array['client']['phone'] = $this->client->phone;
        $this->payment_method && $array['payment_method'] = $this->payment_method->description;
        $array['estimated_date'] = $this->estimated_date;
        $array['created_at'] = $this->created_at->format('Y-m-d');
        /* $array['type'] = $this->type->value; */
        /* $array['type_price'] = $this->price_quote->type_price; */
        $array['vehiculo'] = $this->vehiculo->name;
        unset($array['shipment']);
        unset($array['chasis']);
        unset($array['year']);
        unset($array['type']);
        unset($array['shipment_id']);
        unset($array['invoice_number']);
        unset($array['deposit']);
        unset($array['remito']);
        unset($array['workshop']);
        unset($array['user']);
        return $array;
    }

    public static function toForm($order)
    {
        $base = [
            'id' => $order->id,
            'client_id' => $order->client_id,
            'client' =>  new ClientResource($order->client, 'complete'),
            'type_id' => $order->type_id,
            'brand_id' => $order->vehiculo->brand_id,
            'vehiculo_id' => $order->vehiculo_id,
            'chasis' => $order->chasis,
            'contacto' => $order->contacto,
            'year' => $order->year,

            /* Pedidos General */
            'payment_method_id' => $order->payment_method_id,
            'invoice_number' => $order->invoice_number,

            /* Pedidos Unicos */
            'deposit' => $order->deposit,
            'estimated_date' => $order->estimated_date,

            /* Pedidos siniestro */
            'remito' => $order->remito,
            'workshop' => $order->workshop,

            /* 'type_price_id' => $order->type_price_id,
            'information_source_id' => $order->information_source_id, */
            'observation' => $order->observation,
        ];

        return $base;
    }
}
