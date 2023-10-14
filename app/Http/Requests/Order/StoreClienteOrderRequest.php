<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\TraitRequest;

class StoreClienteOrderRequest extends FormRequest
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
            'type_id' => 'required',
            'detail' => 'required',

            'engine' => 'required',
            /* 'chasis' => 'required', */
            'payment_method_id' => 'required',
            'deposit' => 'required',
            'estimated_date' => 'required',
            /* 'invoice_number' => 'required', */
        ];
    }


    public function messages(): array
    {
        return [
            'client_id.required' => 'El Cliente es requerido',
            'detail.required' => 'Productos es requerido',

            'engine.required' => 'Vehículo/Motor es requerido',
            /* 'chasis.required' => 'Chasis es requerido', */
            'payment_method_id.required' => 'Forma de pago es requerido',
            'deposit.required' => 'Seña es requerido',
            'estimated_date.required' => 'Fecha estimada es requerida',
            /* 'invoice_number.required' => 'Número de factura es requerido', */
        ];
    }
}
