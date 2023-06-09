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
                return $this->complete($array);
            default:
                return $this->default($array);
        }
    }

    private function complete($array)
    {
        $array['observation'] = $this->observation;
        $array['user'] = $this->user;
        $array['client'] = $this->client;

        if ($this->order) {
            $this->order->type;
            $array['order'] = $this->order;
            $array['order']['getGeneralState'] = $this->order->getGeneralState();
        } else {
            $array['order'] = null;
        }

        $array['price_quotes_products'] = PriceQuoteProductResource::collection($this->detail);

        return $array;
    }

    private function default($array)
    {
        $array['user'] = $this->user->name;
        $array['client'] = $this->client;
        $array['state'] = $this->order ? $this->order->type->value : null;

        return $array;
    }
}
