<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\BaseTableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            'responsable' => $this->responsable ? $this->responsable->name : null,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'origen' => $this->origen,
            'resolucion' => $this->resolucion,
            'created_at' => $this->created_at,
        ];
    }
}
