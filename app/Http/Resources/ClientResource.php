<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
        $withOrder = (bool) $request->query('withOrder');

        if (!$data_type) {
            $withOrder && $array['orders'] = $this->orders;
            $array['city'] = $this->city;
        }

        if ($data_type && $data_type == 'table') {
            $array['city'] = $this->city;
            $array['province'] = $this->city ? $this->city->province : null;
        }

        return $array;
    }
}
