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
    public function agregarArticulo(array $data, $numero_interno)
    {
        $_data = [
            "nroInterno" => $numero_interno,
            "idProducto" => $data['idProducto'],
            "cantidad" =>  $data['cantidad'],
            "descuento" => 0,
            "precio" => $data['precio'],
            /* !! */
            "unidad" => 0,
            "unidad1" => 0,
            "bultos" => 0,
            "despacho" => "N",
            "comision" => 0,
            "idPresupuestos" => 0,
            "camposAdicionales" => []

        ];
        return $this->post('Pedido/AgregarArticulo', $_data);
    }
}
