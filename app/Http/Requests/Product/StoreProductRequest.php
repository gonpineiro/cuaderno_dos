<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    use ProductTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->authBrand();
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
            'provider_code' => 'required|max:25',
            'factory_code' => 'required|max:25',
            'equivalence' => 'required|max:255',

            'description' => 'required|max:500',
            'model' => 'required|max:25',
            'engine' => 'required|max:25',
            'observation' => 'required|max:500',

            'min_stock' => 'required|boolean',
            'empty_stock' => 'required|boolean',

            'ship' => 'required|max:5',
            'module' => 'required|max:5',
            'side' => 'required|max:5',
            'column' => 'required|max:5',
            'row' => 'required|max:5',

            'provider_id' => 'required',
            'brand_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'code es requerido',
            'code.max' => 'code no debe superar el maximo de 25 caracteres',
            'code.unique' => 'code ya se encuentra registrado',

            'factory_code.required' => 'factory_code es requerido',
            'factory_code.max' => 'factory_code no debe superar el maximo de 25 caracteres',

            'equivalence.required' => 'equivalence es requerido',
            'equivalence.max' => 'equivalence no debe superar el maximo de 25 caracteres',

            'description.required' => 'description es requerido',
            'description.max' => 'description no debe superar el maximo de 500 caracteres',

            'model.required' => 'model es requerido',
            'model.max' => 'model no debe superar el maximo de 25 caracteres',

            'engine.required' => 'engine es requerido',
            'engine.max' => 'engine no debe superar el maximo de 25 caracteres',

            'observation.required' => 'observation es requerido',
            'observation.max' => 'observation no debe superar el maximo de 500 caracteres',

            'min_stock.required' => 'min_stock es requerido',
            'min_stock.boolean' => 'min_stock debe ser un booleano',

            'empty_stock.required' => 'empty_stock es requerido',
            'empty_stock.boolean' => 'empty_stock debe ser un booleano',

            'ship.required' => 'ship es requerido',
            'ship.max' => 'ship no debe superar el maximo de 5 caracteres',

            'module.required' => 'module es requerido',
            'module.max' => 'module no debe superar el maximo de 5 caracteres',

            'side.required' => 'side es requerido',
            'side.max' => 'side no debe superar el maximo de 5 caracteres',

            'column.required' => 'column es requerido',
            'column.max' => 'column no debe superar el maximo de 5 caracteres',

            'row.required' => 'row es requerido',
            'row.max' => 'row no debe superar el maximo de 5 caracteres',

            'provider_id.required' => 'provider_id es requerido',
            'provider_id.integer' => 'provider_id debe ser un entero',

            'brand_id.required' => 'brand_id es requerido',
            'brand_id.integer' => 'brand_id debe ser un entero',
        ];
    }
}
