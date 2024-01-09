<?php

namespace App\Http\Requests\PriceQuote;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePriceQuoteRequest extends FormRequest
{
    use TraitRequest;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => 'required',
            'detail' => 'required',
            /* 'engine' => 'required|max:200', */
            'type_price_id' => 'required',
            'vehiculo_id' => 'required',
            'information_source_id' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'client_id.required' => 'El Cliente es requerido',
            'detail.required' => 'Productos es requerido',

            /* 'engine.required' => 'Vehículo/Motor es requerido',
            'engine.max' => 'Motor no debe superar el maximo de 200 caracteres', */

            'type_price_id.required' => 'Tipo precio requerido',
            'information_source_id.required' => 'Medio de consulta es requerido',

            'year.max' => 'Año Inválido',
            'year.min' => 'Año Inválido',

            'vehiculo_id.required' => 'Vehículo es requerida',
        ];
    }
}
