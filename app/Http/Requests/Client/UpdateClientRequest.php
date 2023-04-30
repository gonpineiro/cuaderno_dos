<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientRequest extends FormRequest
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
            'dni' => 'required|max:8|min:8',
            'name' => 'required|max:35',
            'email' => 'required|email|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'dni.required' => 'documento es requerido',
            'dni.max' => 'formato del documento es invalido',
            'dni.min' => 'formato del documento es invalido',

            'name.required' => 'nombre es requerido',
            'name.max' => 'nombre no debe superar los 35 caracteres',

            'email.required' => 'correo electronico es requerido',
            'email.max' => 'correo electronico no debe superar los 100 caracteres',
            'email.email' => 'correo electronico invalido',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendResponse(null, $validator->errors(), 422));
    }
}
