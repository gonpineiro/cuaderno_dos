<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
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
            'type_id' => 'required',
            'orders_products' => 'required',

            'engine' => 'required',
            'chasis' => 'required',
            'payment_method' => 'required',
            'invoice_number' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'client_id.required' => 'El Cliente es requerido',
            'orders_products.required' => 'Productos es requerido',

            'engine.required' => 'Vehículo/Motor es requerido',
            'chasis.required' => 'Chasis es requerido',
            'payment_method.required' => 'Forma de pago es requerido',
            'invoice_number.required' => 'Número de factura es requerido',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendResponse(null, $validator->errors(), 422));
    }
}
