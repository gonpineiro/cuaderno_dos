<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\BaseTableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientJazzResource extends JsonResource
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
            'IdCliente' => $this->IdCliente,
            'Nombre' => $this->Nombre,
            'Domicilio' => $this->Domicilio,
            'CP' => $this->CP,
            'Localidad' => $this->Localidad,
            'Mail' => $this->Mail,
            'CUIT' => $this->CUIT,
            'Telefono' => $this->Telefono,
            'TelParticular' => $this->TelParticular,
            'TelCelular' => $this->TelCelular,
            'FechaNacimiento' => $this->FechaNacimiento,
        ];
    }
}
