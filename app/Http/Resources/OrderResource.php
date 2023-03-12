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
        $array['user'] = $this->user->name;
        $array['type'] = $this->user->type;
        $array['detail'] =  OrderProduct::collection($this->detail);
        $array['count_pendientes'] = 100;

        return $array;
    }
}
