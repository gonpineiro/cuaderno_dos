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
        return [
            'dni' => 'required|max:8|min:8|unique:clients',
            'name' => 'required|max:35',
            'email' => 'required|email|max:100|unique:clients',
            'city_id' => 'required',
        ];
    }

    public function messages(): array
    {
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
