<?php

namespace App\Http\Resources\Shipment;

use App\Http\Resources\ClientResource;
use App\Models\Table;
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

        $this->vehiculo->brand;
        $array['vehiculo'] = $this->vehiculo;

        $this->payment_method && $array['payment_method'] = $this->payment_method;

        $this->order->type;
        $array['order'] = $this->order;

        $array['price_quote'] = $this->price_quote;

        $array['init_state'] = Table::where('name', 'order_envio_state')->where('value', 'pendiente')->first();

        $array['detail'] = ShipmentProductResource::collection($this->detail);
        /* $array['percentages'] = $this->getPercentages(); */

        return $array;
    }

    private function default($array)
    {
        unset($array['description']);
        $array['user'] = $this->user->name;
        $this->client->city;
        $array['payment_method'] = $this->payment_method;
        $array['client'] = $this->client;

        return $array;
    }

    public static function toForm($shipment)
    {
        return [
            'id' => $shipment->id,
            'client_id' => $shipment->client_id,
            'client' =>  new ClientResource($shipment->client, 'complete'),
            'brand_id' => $shipment->vehiculo->brand_id,
            'vehiculo_id' => $shipment->vehiculo_id,
            'chasis' => $shipment->chasis,
            'contacto' => $shipment->contacto,
            'year' => $shipment->year,
            'payment_method_id' => $shipment->payment_method_id,
            'transport' => $shipment->transport,
            'invoice_number' => $shipment->invoice_number,
            'nro_guia' => $shipment->nro_guia,
            'bultos' => $shipment->bultos,
            'send_adress' => $shipment->send_adress,
        ];
    }

    public static function pdfArray($detail, $truncate_int)
    {
        $array = [];

        foreach ($detail as $value) {

            $array[] = [
                'amount' => $value->amount,
                'code' => $value->product->code,
                'description' => truncateString($value->product->description, $truncate_int),
                'unit_price' =>  $value->unit_price,
                'total' =>  $value->unit_price * $value->amount,
            ];
        }

        return $array;
    }

    public static function formatPdf($detail)
    {
        foreach ($detail as $key => $value) {
            $detail[$key]['unit_price'] = formatoMoneda($value['unit_price']);
            $detail[$key]['total'] = formatoMoneda($value['total']);
        }

        return $detail;
    }
}
