<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
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
            'name' => 'required|max:25',
            'email' => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name es requerido',
            'name.max' => 'name no debe superar el maximo de 25 caracteres',

            'email.required' => 'email es requerido',
            'email.max' => 'email no debe superar el maximo de 255 caracteres',
        ];
    }
}
