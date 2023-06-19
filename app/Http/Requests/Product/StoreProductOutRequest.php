<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductOutRequest extends FormRequest
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
            'code' => 'required|max:25|unique:products',
            'description' => 'required|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'code es requerido',
            'code.max' => 'code no debe superar el maximo de 25 caracteres',
            'code.unique' => 'code ya se encuentra registrado',

            'description.required' => 'description es requerido',
            'description.max' => 'description no debe superar el maximo de 500 caracteres',
        ];
    }
}
