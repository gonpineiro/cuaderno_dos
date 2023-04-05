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
            'orders_products' => 'required',
            'type_id' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'orders_products.required' => 'Requiere seleccionar al menos un producto',
            'client_id.required' => 'Debe seleccionar un cliente',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendResponse(null, $validator->errors(), 422));
    }
}
