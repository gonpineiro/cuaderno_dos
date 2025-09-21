<?php

namespace App\Services\JazzServices;

class PedidoService extends ApiService
{
    public function sayHello()
    {
        return $this->get('Producto/SayHello');
    }

    public function agregarPedido(array $data)
    {
        return $this->post('Pedido/AgregarPedido', $data);
    }
    public function agregarArticulo(array $data)
    {
        $_data = [
            "nroInterno" => $data['nroInterno'],
            "idProducto" => $data['idProducto'],
            "cantidad" => $data['cantidad'],
            "precio" => $data['precio'],
            /* !! */
            "descuento" => 0,
            "unidad" => 0,
            "unidad1" => 0,
            "bultos" => 0,
            "despacho" => "string",
            "comision" => 0,
            "idPresupuestos" => 0,

        ];
        return $this->post('Pedido/AgregarArticulo', $_data);
    }

    /* {

    return [
                    "id"   => $detail->id,
                    "idProducto"  => $detail->product->idProducto ?? null,
                    "precio" => $detail->unit_price,
                    "cantidad" => $detail->amount
                ];
    "nroInterno": 388537,
    "idProducto": 27,
    "cantidad": 1,
    "precio": 10000,
    "descuento": 0,
    "unidad": 0,
    "unidad1": 0,
    "bultos": 0,
    "despacho": "string",
    "comision": 0,
    "idPresupuestos": 0
} */

    public function agregarArticulos() {}
}
