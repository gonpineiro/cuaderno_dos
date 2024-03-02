<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\TraitRequest;

class StoreSiniestroOrderRequest extends FormRequest
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

            'chasis' => 'required',
            'remito' => 'required',
            'workshop' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'client_id.required' => 'El Cliente es requerido',
            'detail.required' => 'Productos es requerido',

            'chasis.required' => 'Chasis es requerido',
            'remito.required' => 'Remito es requerido',
            'workshop.required' => 'Taller es requerido',
        ];
    }
}
