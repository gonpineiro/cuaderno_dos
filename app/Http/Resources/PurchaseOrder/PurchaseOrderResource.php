<?php

namespace App\Http\Resources\PurchaseOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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

    private function default($array)
    {
        unset($array['description']);
        unset($array['detail']);
        /* $array['user'] = $this->user->name; */

        $array['state'] = $this->state;
        $array['provider'] = $this->provider->name;

        return $array;
    }

    private function complete($array)
    {
        $array['state'] = $this->state;
        $array['provider'] = $this->provider;
        $array['detail'] = PurchaseOrderProductResource::collection($this->detail);

        return $array;
    }
}
