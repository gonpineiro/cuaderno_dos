<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
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
            'payment_method_id' => 'required',
            'invoice_number' => 'required',
            'transport' => 'max:100',
            'nro_gruia' => 'max:100',
            'bultos' => 'int|max:50',
            'send_adress' => 'max:100'
        ];
    }


    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'Forma de pago es requerido',
            'invoice_number.required' => 'Número de factura es requerido',

            'transport.max' => 'Transporte no puede superar los 100 caracteres',

            'nro_gruia.max' => 'Nro de guia no puede superar los 100 caracteres',

            'bultos.int' => 'Bultos debe ser un número',
            'bultos.max' => 'Bultos no puede superar los 50',

            'send_adress.max' => 'Nro de guia no puede superar los 100 caracteres',
        ];
    }
}
