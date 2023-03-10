<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|max:10',
            'provider_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name es requerido',
            'name.max' => 'name no debe superar el maximo de 10 caracteres',

            'provider_id.required' => 'provider_id es requerido',
            'provider_id.integer' => 'provider_id debe ser un entero',
        ];
    }
}
