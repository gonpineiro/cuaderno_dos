<?php

namespace App\Http\Requests\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\TraitRequest;

class StoreVehiculoRequest extends FormRequest
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
            ],
            'brand_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ciudad es requerido',
            'name.max' => 'ciudad no debe superar los 35 caracteres',

            'province.required' => 'provincia es requerido',
            'province.max' => 'provincia 35 caracteres',

        ];
    }
}
