<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    use ProductTrait, TraitRequest;

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
            'provider_code' => 'required|max:25',
            'factory_code' => 'required|max:25',
            'equivalence' => 'required|max:255',

            'description' => 'required|max:500',
            'model' => 'required|max:25',
            'engine' => 'required|max:200',
            'observation' => 'required|max:500',

            'min_stock' => 'boolean',
            'empty_stock' => 'boolean',

            'ship' => 'required|max:5',
            'module' => 'required|max:5',
            'side' => 'required|max:5',
            'column' => 'required|max:5',
            'row' => 'required|max:5',

            'provider_id' => 'required',
            'brand_id' => 'required',
            'state_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Código es requerido',
            'code.max' => 'Código no debe superar el maximo de 25 caracteres',
            'code.unique' => 'Código ya se encuentra registrado',

            'factory_code.required' => 'Código de fabrica es requerido',
            'factory_code.max' => 'Código de fabrica no debe superar el maximo de 25 caracteres',

            'equivalence.required' => 'Equivalencia es requerido',
            'equivalence.max' => 'Equivalencia no debe superar el maximo de 25 caracteres',

            'description.required' => 'Description es requerido',
            'description.max' => 'Description no debe superar el maximo de 500 caracteres',

            'model.required' => 'Modelo es requerido',
            'model.max' => 'Modelo no debe superar el maximo de 25 caracteres',

            'engine.required' => 'Motor es requerido',
            'engine.max' => 'Motor no debe superar el maximo de 200 caracteres',

            'observation.required' => 'Observación es requerido',
            'observation.max' => 'Observación no debe superar el maximo de 500 caracteres',

            'min_stock.required' => 'min_stock es requerido',
            'min_stock.boolean' => 'min_stock debe ser un booleano',

            'empty_stock.required' => 'empty_stock es requerido',
            'empty_stock.boolean' => 'empty_stock debe ser un booleano',

            'ship.required' => 'Nave es requerido',
            'ship.max' => 'Nave no debe superar el maximo de 5 caracteres',

            'module.required' => 'Modulo es requerido',
            'module.max' => 'Modulo no debe superar el maximo de 5 caracteres',

            'side.required' => 'Lado es requerido',
            'side.max' => 'Lado no debe superar el maximo de 5 caracteres',

            'column.required' => 'Columna es requerido',
            'column.max' => 'Columna no debe superar el maximo de 5 caracteres',

            'row.required' => 'Fila es requerido',
            'row.max' => 'Fila no debe superar el maximo de 5 caracteres',

            'provider_id.required' => 'Proveedor es requerido',
            'provider_id.integer' => 'Proveedor debe ser un entero',

            'brand_id.required' => 'Marca es requerido',
            'brand_id.integer' => 'Marca debe ser un entero',

            'state_id.required' => 'Estado de stock es requerido',
            'state_id.integer' => 'Marca debe ser un entero',
        ];
    }
}
