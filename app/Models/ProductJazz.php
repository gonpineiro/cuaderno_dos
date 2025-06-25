<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public static function cleanTemp()
    {
        $total = DB::table('product_jazz_temp')->count();

        $deletedCount = DB::affectingStatement(
            "
                    DELETE t
                    FROM product_jazz_temp t
                    JOIN product_jazz p ON p.id = t.id
                    WHERE
                        p.nombre          <=> t.nombre          AND
                        p.code            <=> t.code            AND
                        p.provider_code   <=> t.provider_code   AND
                        p.equivalence     <=> t.equivalence     AND
                        p.observation     <=> t.observation     AND
                        p.ubicacion       <=> t.ubicacion       AND
                        p.stock           =   t.stock           AND
                        p.precio_lista_2  <=> t.precio_lista_2  AND
                        p.precio_lista_3  <=> t.precio_lista_3  AND
                        p.precio_lista_6  <=> t.precio_lista_6  AND
                        p.fecha_alta      <=> t.fecha_alta      AND
                        p.fecha_mod       <=> t.fecha_mod"
        );

        return ['deleted_count' => $deletedCount, 'total' => $total];
    }

    public static function processTemp()
    {
        $total = DB::table('product_jazz_temp')->count();

        // 1. no_requiere
        $noRequiere = DB::affectingStatement("
                    UPDATE product_jazz_temp t
                    JOIN product_jazz p ON p.id = t.id
                    SET t.state = 'no_requiere'
                    WHERE
                        t.state = 'en_proceso' AND
                        p.nombre          <=> t.nombre          AND
                        p.code            <=> t.code            AND
                        p.provider_code   <=> t.provider_code   AND
                        p.equivalence     <=> t.equivalence     AND
                        p.observation     <=> t.observation     AND
                        p.ubicacion       <=> t.ubicacion       AND
                        p.stock           =   t.stock           AND
                        p.precio_lista_2  <=> t.precio_lista_2  AND
                        p.precio_lista_3  <=> t.precio_lista_3  AND
                        p.precio_lista_6  <=> t.precio_lista_6  AND
                        p.fecha_alta      <=> t.fecha_alta      AND
                        p.fecha_mod       <=> t.fecha_mod
                ");

        // 2. requiere
        $requiere = DB::affectingStatement("
                    UPDATE product_jazz_temp t
                    JOIN product_jazz p ON p.id = t.id
                    SET t.state = 'requiere'
                    WHERE t.state = 'en_proceso'
                ");

        // 3. nuevo
        $nuevo = DB::affectingStatement("
                    UPDATE product_jazz_temp t
                    LEFT JOIN product_jazz p ON p.id = t.id
                    SET t.state = 'nuevo'
                    WHERE p.id IS NULL AND t.state = 'en_proceso'
                ");

        return [
            'total' => $total ?? 0,
            'no_requiere' => $noRequiere ?? 0,
            'requiere' => $requiere ?? 0,
            'nuevo' => $nuevo ?? 0,
        ];
    }
}
