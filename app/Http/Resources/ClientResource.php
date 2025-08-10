<?php

namespace App\Http\Resources;

use App\Http\Resources\Client\ClientConfigResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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

        /* $data_type = $request->query('data_type');
        $withOrder = (bool) $request->query('withOrder');

        if (!$data_type) {
            $withOrder && $array['orders'] = $this->orders;
            $array['city'] = new CityResource($this->city);
        }

        if ($data_type && $data_type == 'table') {
            $array['city'] = $this->city;
            $array['province'] = $this->city ? $this->city->province : null;
        }

        return $array;
        */
    }

    private function complete($array)
    {
        $array['city'] = new CityResource($this->city);

        /* $this->vehiculo && $this->vehiculo->brand;
        $array['vehiculo'] = $this->vehiculo; */

        $this->vehiculos->load('vehiculo.brand');
        $array['vehiculos'] = $this->vehiculos;
        $array['condicion_iva'] = $this->condicion_iva;
        $array['config'] = ClientConfigResource::collection($this->config);
        return $array;
    }

    private function default($array)
    {
        $array['city'] = new CityResource($this->city);
        $this->vehiculos->load('vehiculo.brand');
        $array['vehiculos'] = $this->vehiculos;
        return $array;
    }
}
