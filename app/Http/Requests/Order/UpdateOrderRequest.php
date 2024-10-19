<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\TraitRequest;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        /* $type = Table::find($this->type_id);

        $com = [
            'client_id' => 'required',
            'engine' => 'required|max:200',
            'chasis' => 'required|max:200',
        ];

        if ($type->value == 'cliente') {
            $com['deposit'] = 'required';
            $com['estimated_date'] = 'required';
            return $com;
        } else if ($type->value == 'online') {
            $com['payment_method_id'] = 'required';
            $com['invoice_number'] = 'required';
            return $com;
        } else if ($type->value == 'siniestro') {
            $com['remito'] = 'required';
            $com['workshop'] = 'required';
            return $com;
        } */

        return [];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es requerido',

            'engine.required' => 'Vehículo/Motor es requerido',
            'engine.max' => 'Motor no debe superar el maximo de 200 caracteres',

            'chasis.required' => 'Chasis es requerido',
            'chasis.max' => 'Chasis no debe superar el maximo de 200 caracteres',

            'deposit.required' => 'La seña es requerida',
            'estimated_date.required' => 'La fecha estimada es requerido',

            'payment_method_id.required' => 'Metodo de pago es requerida',
            'invoice_number.required' => 'Número de factura es requerido',

            'remito.required' => 'Remito es requerido',
            'workshop.required' => 'Taller es requerido',
        ];
    }
}
