<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\TraitRequest;

class StoreCityRequest extends FormRequest
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
        $cityId = $this->route('city'); // Obtener el ID de la ciudad de la ruta

        return [
            'name' => [
                'required',
                'max:35',
                Rule::unique('cities')->where(function ($query) {
                    // Agregar la cláusula WHERE para verificar la combinación name y province
                    $query->where('province', $this->input('province'));
                })->ignore($cityId),
            ],
            'province' => 'required|max:35',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ciudad es requerido',
            'name.max' => 'ciudad no debe superar los 35 caracteres',
            'name.unique' => 'La combinación de nombre y provincia ya existe.',

            'province.required' => 'provincia es requerido',
            'province.max' => 'provincia 35 caracteres',


        ];
    }
}
