<?php

namespace App\Http\Requests\Provider;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
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
            'name' => 'required|max:25',
            'email' => 'required|email|unique:providers|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nombre es requerido',
            'name.max' => 'Nombre no debe superar el maximo de 25 caracteres',

            'email.required' => 'Email es requerido',
            'email.email' => 'Correo electronico invalido',
            'email.unique' => 'Ya existe un proveedor con ese correo electronico',
            'email.max' => 'Correo electronico no debe superar el maximo de 255 caracteres',
        ];
    }
}
