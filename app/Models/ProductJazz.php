<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductJazz extends Model
{

    protected $table = 'product_jazz';

    protected $fillable = [
        'id',
        'nombre',
        'stock',
        'precio_lista_2',
        'precio_lista_3',
        'precio_lista_6',

        //camposAdicionales
        'code',
        'provider_code',
        'equivalence',
        'observation',
        'ubicacion',

        'fecha_alta',
        'fecha_mod'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function setPrices(Collection $precios)
    {
        $this->precio_lista_2 = $this->extractPrice($precios, 2);
        $this->precio_lista_3 = $this->extractPrice($precios, 3);
        $this->precio_lista_6 = $this->extractPrice($precios, 6);
    }

    private function extractPrice(Collection $prices, int $listId)
    {
        return optional($prices->firstWhere('idLista', $listId))['precio'] ?? null;
    }

    public function setAdicionales(Collection $adicionales)
    {
        // Mapeo de camposAdicionales → atributos en el modelo
        $camposMap = [
            'CODIGO ORIGINAL'    => 'code',
            'CODIGO PROVEEDOR'   => 'provider_code',
            'EQUIVALENCIA'       => 'equivalence',
            'OBSERVACIONES'      => 'observation',
            'UBICACION'          => 'ubicacion',
        ];

        foreach ($adicionales ?? [] as $campo) {
            $atributo = $camposMap[$campo['nombre']] ?? null;

            if (in_array($atributo, $this->getFillable())) {
                $this->$atributo = $campo['valor'];
            }
        }
    }
}
