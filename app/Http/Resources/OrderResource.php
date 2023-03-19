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
        $array['type'] = $this->type->value;
        $array['percentages'] = $this->getPercentages();

        /* online */
        if ($this->type->value == 'online') {
            $array['detail'] = $this->online($request);
        }

        /* Pedido */
        if ($this->type->value == 'pedido') {
            $array['detail'] = $this->pedido($request);
        }

        /* Siniestro */
        if ($this->type->value == 'siniestro') {
            $array['detail'] = $this->siniestro($request);
        }

        return $array;
    }

    private function online($request)
    {
        return OrderProduct::collection($this->detail);
    }

    private function pedido($request)
    {
        return OrderProduct::collection($this->detail);
    }

    private function siniestro($request)
    {
        return OrderProduct::collection($this->detail);
    }
}
