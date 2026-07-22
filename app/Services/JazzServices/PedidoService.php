<?php

namespace App\Services\JazzServices;

use App\Models\Order;
use App\Models\OrderProduct;

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

    public function crearPedidoCompleto($data, $order)
    {
        $pedido = $this->agregarPedido($data);
        if (!isset($pedido['refID']) || !$pedido['refID']) {
            throw new \Exception('No se logro crear el pedido');
        }

        $id_pedido_jazz = $pedido['refID'];

        $productData = $this->getJazzData($order->detail, $id_pedido_jazz);

        foreach ($productData as $data) {
            $pedido_producto = $this->agregarArticulo($data, $id_pedido_jazz);

            if ($pedido_producto["refID"] == 0) {
                throw new \Exception('Es probable que el producto no exista en Jazz');
            }

            $order_product = OrderProduct::find($data["id"]);
            if ($order_product) {
                $order_product->ref_jazz_id = $pedido_producto["refID"];
                $order_product->save();
            }
        }

        $finalizar = $this->finalizarPedido($id_pedido_jazz);

        if (isset($finalizar['hasErrorMessage']) && $finalizar['hasErrorMessage']) {
            throw new \Exception($finalizar['responseMessage']);
        }

        return $id_pedido_jazz;
    }

    /**
     * Obtiene la informacion basica para adjuntar a un pedido del Jazz
     * @param int $nroInterno | id del pedido del jazz,
     */
    public function getJazzData($detail, $nroInterno)
    {
        return $detail->map(function ($detail) use ($nroInterno) {
            $idProducto = $detail->product->idProducto ? $detail->product->idProducto : 2;

            return [
                "id"   => $detail->id,
                'product_id' => $detail->product_id,
                "idProducto"  =>  $idProducto,
                "precio" => $detail->unit_price,
                "cantidad" => $detail->amount,
                "detalle" => $detail->description,
                "nroInterno" => $nroInterno
            ];
        })
            //->filter(fn($item) => !empty($item["idProducto"]))
            ->values()
            ->toArray();
    }


    public function agregarArticulo(array $data, $numero_interno)
    {
        $_data = [
            "nroInterno" => $numero_interno,
            "idProducto" => $data['idProducto'],
            "cantidad" =>  $data['cantidad'],
            "descuento" => 0,
            "detalle" => $data['detalle'],
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

    public function finalizarPedido($numero_interno, string $descuento = '')
    {
        $_data = [
            "nroInterno" => $numero_interno,
            "descuento" => $descuento,
        ];
        return $this->post('Pedido/FinalizarPedido', $_data);
    }

    public function getFormatData(Order $order, $cliente_jazz_id, string $observation = null)
    {
        $user = auth()->user();
        $typePrice = optional(optional($order->price_quote)->type_price)->value;
        // Regla de negocio actual hardcodeada para Jazz:
        // contado -> lista 6, lista -> lista 2. Si no hay type_price, se mantiene 6 por compatibilidad.
        $idLista = $typePrice === 'lista' ? 2 : 6;

        return [
            "empresa" => 1,
            "sucursal" => 2,
            "letra" => "P",
            "boca" => 0,
            "idCliente" => $cliente_jazz_id,
            "ivaTipo" => 3,
            "idVendedor" => $user->idVendedor ? $user->idVendedor : 1,
            "vendedorComision" => 0,
            "idLista" => $idLista,
            "obs" => $observation,
            "condicion" => 0,
            "moneda" => 1,
            "enMostrador" => "S",
            "fecha" => \Carbon\Carbon::now('UTC')->format('Y-m-d\TH:i:s.v\Z'),
            "descuento" => null,
            "recargo" => null,
            "idEstado" => 5
        ];
    }
}
