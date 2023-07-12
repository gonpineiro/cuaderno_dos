<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\TraitRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClientRequest extends FormRequest
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
        if ($this->input('is_insurance')) {
            return [
                'name' => 'required|max:35',
                'dni' => 'max:11|min:11|unique:clients',
            ];
        }

        return [
            'name' => 'required|max:35',
            'dni' => 'required|max:8|min:8|unique:clients',
            'email' => 'required|email|max:100|unique:clients',
            'city_id' => 'required',
        ];
    }

    public function messages(): array
    {
        if ($this->input('is_insurance')) {
            return [
                'dni.max' => 'Formato del CUIT es invalido',
                'dni.min' => 'Formato del CUIT es invalido',
                'dni.unique' => 'CUIT ya se encuentra registrado',

                'name.required' => 'Nombre es requerido',
                'name.max' => 'Nombre no debe superar los 35 caracteres',
            ];
        }
        return [
            'dni.required' => 'Documento es requerido',
            'dni.max' => 'Formato del documento es invalido',
            'dni.min' => 'Formato del documento es invalido',
            'dni.unique' => 'Documento ya se encuentra registrado',

            'name.required' => 'Nombre es requerido',
            'name.max' => 'Nombre no debe superar los 35 caracteres',

            'email.required' => 'Correo electronico es requerido',
            'email.max' => 'Correo electronico no debe superar los 100 caracteres',
            'email.email' => 'Correo electronico invalido',
            'email.unique' => 'Correo electronico ya existente',

            'city_id.required' => 'La ciudad es requerida',
        ];
    }
}
