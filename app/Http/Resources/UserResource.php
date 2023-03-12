<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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

        if ($request->query('ordenes') == "true") {
            $array['orders'] = $this->orders;
        }

        if ($request->query('ordenes_detalle') == "true") {
            $array['orders'] = OrderResource::collection($this->orders);
        }
        return $array;
    }
}
