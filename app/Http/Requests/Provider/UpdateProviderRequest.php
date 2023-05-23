<?php

namespace App\Http\Requests\Provider;

use App\Http\Requests\TraitRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProviderRequest extends FormRequest
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
            'email' => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nombre es requerido',
            'name.max' => 'Nombre no debe superar el maximo de 25 caracteres',

            'email.required' => 'Email es requerido',
            'email.max' => 'Email no debe superar el maximo de 255 caracteres',
        ];
    }
}
