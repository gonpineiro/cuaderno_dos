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

            'engine' => 'required',
            'chasis' => 'required',
            'type_price' => 'required',
            'information_source' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'client_id.required' => 'El Cliente es requerido',
            'detail.required' => 'Productos es requerido',

            'engine.required' => 'Vehículo/Motor es requerido',
            'chasis.required' => 'Chasis es requerido',
            'type_price.required' => 'Tipo precio requerido',
            'information_source.required' => 'Medio de consulta es requerido',
        ];
    }
}