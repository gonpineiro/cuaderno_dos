<?php

namespace App\Http\Resources\Shipment;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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

        $this->payment_method && $array['payment_method'] = $this->payment_method;

        $this->order->type;
        $array['order'] = $this->order;

        $array['price_quote'] = $this->price_quote;

        $array['detail'] = ShipmentProductResource::collection($this->detail);
        $array['percentages'] = $this->getPercentages();

        return $array;
    }

    private function default($array)
    {
        unset($array['description']);
        $array['user'] = $this->user->name;
        $array['client'] = $this->client;

        return $array;
    }
}