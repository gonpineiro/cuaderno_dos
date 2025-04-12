<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Product\ProductResource;

class OrderProductResource extends JsonResource
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

        $array['product'] = new ProductResource($this->product);

        $array['state'] =  $this->state;
        $array['provider'] =  $this->provider;

        return $array;
    }

    public static function pdfArray($detail)
    {
        $array = [];

        foreach ($detail as $value) {

            $desc = $value->description ? $value->description : $value->product->description;
            $array[] = [
                'code' => $value->product->code,
                'ubication' => $value->product->ubication,
                'description' => truncateString($desc, 50),
                'amount' => $value->amount,
                'unit_price' => $value->unit_price,
                'total' => $value->unit_price * $value->amount,
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

    public static function emailPedidoArray($detail)
    {
        $array = [];

        foreach ($detail as $value) {

            $desc = $value->description ? $value->description : $value->product->description;
            $array[] = [
                'amount' => $value->amount,
                'description' => truncateString($desc, 50),
                'unit_price' => $value->unit_price,
                'total' => $value->unit_price * $value->amount,
            ];
        }

        return $array;
    }
}
