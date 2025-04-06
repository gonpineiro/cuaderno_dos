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

    public static function pdfArray($detail, Coeficiente $coef = null, $truncate_int, $redondear = true)
    {
        $array = [];

        foreach ($detail as $value) {

            $valor = $coef ? $value->unit_price * $coef->coeficiente * $coef->value : $value->unit_price;


            $unitario = $redondear ?  redondearNumero($valor) : $valor;

            $desc = $value->description ? $value->description : $value->product->description;
            $array[] = [
                'code' => $value->product->code,
                'description' => truncateString($desc, $truncate_int),
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
