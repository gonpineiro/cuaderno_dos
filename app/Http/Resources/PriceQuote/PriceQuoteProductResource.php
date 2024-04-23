<?php

namespace App\Http\Resources\PriceQuote;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Product\ProductResource;
use App\Models\Coeficiente;

class PriceQuoteProductResource extends JsonResource
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

        $array['product'] =  new ProductResource($this->product);

        $array['state'] =  $this->state;
        $array['provider'] =  $this->provider;

        return $array;
    }

    public static function pdfArray($detail, Coeficiente $coef = null)
    {
        $array = [];

        foreach ($detail as $value) {

            $unitario = redondearNumero($coef ? $value->unit_price * $coef->coeficiente * $coef->value : $value->unit_price);

            $array[] = [
                'code' => $value->product->code,
                'description' => $value->product->ubication,
                'amount' => $value->amount,
                'unit_price' => $unitario,
                'total' => $unitario * $value->amount,
                'ubication' => $value->product->ubication,
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
