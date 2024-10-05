<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\TraitRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientRequest extends FormRequest
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
                'name' => 'required|max:255',
                'cuit' => 'required|max:11|min:11',
            ];
        }

        if ($this->input('is_company')) {
            return [
                'name' => 'required|max:255',
                'cuit' => 'required|max:11|min:11',
                'email' => 'required|email|max:255',
                'phone' => 'required|max:255',
            ];
        }

        return [
            'name' => 'required|max:255',
            /* 'dni' => 'required|max:8|min:8', */
            'dni' => 'max:8',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:150',
            'city_id' => 'required',
        ];
    }

    public function messages(): array
    {
        if ($this->input('is_insurance')) {
            return [
                'cuit.max' => 'Formato del CUIT es invalido',
                'cuit.min' => 'Formato del CUIT es invalido',
                'cuit.unique' => 'CUIT ya se encuentra registrado',

                'name.required' => 'Nombre es requerido',
                'name.max' => 'Nombre no debe superar los 35 caracteres',
            ];
        }
        return [
            /* 'dni.required' => 'Documento es requerido', */
            'dni.max' => 'Formato del documento es invalido',
            /* 'dni.min' => 'Formato del documento es invalido', */
            /*  'dni.unique' => 'Documento ya se encuentra registrado', */

            'name.required' => 'Nombre es requerido',
            'name.max' => 'Nombre no debe superar los 35 caracteres',

            'phone.required' => 'Teléfono es requerido',
            'phone.max' => 'Teléfono no debe superar los 40 caracteres',

            'email.required' => 'Correo electronico es requerido',
            'email.max' => 'Correo electronico no debe superar los 100 caracteres',
            'email.email' => 'Correo electronico invalido',
            'email.unique' => 'Correo electronico ya existente',

            'city_id.required' => 'La ciudad es requerida',
        ];
    }
}
