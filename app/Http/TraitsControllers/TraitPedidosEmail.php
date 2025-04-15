<?php

namespace App\Http\TraitsControllers;

use Illuminate\Support\Facades\Mail;

use App\Models\Order;
use App\Models\Shipment;

use App\Mail\CrearPedidoProductoUnicoEmail;
use App\Mail\EnvioDespachadoEmail;
use App\Mail\PurchaseOrderEmail;
use App\Mail\PedidoRetirarEmail;
use App\Mail\PedidoEntregadoEmail;
use App\Mail\PedidoOnlineEntregadoEmail;
use App\Mail\PedidoUnicoRetirarEmail;
use App\Models\PurchaseOrder;

/* Nicolasallende90@gmail.com */

trait TraitPedidosEmail
{
    /** 1 - Cuando se crea un pedido con un producto unico */
    public static function pedidoProductoUnico(Order $pedido)
    {
        $correo = new CrearPedidoProductoUnicoEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new CrearPedidoProductoUnicoEmail($pedido));
        return $correo->render();
    }

    /** 2 - Cuando esta listo para retirar un pedido con producto unico  */
    public static function pedidoUnicoRetirar(Order $pedido)
    {
        $correo = new PedidoUnicoRetirarEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new PedidoUnicoRetirarEmail($pedido));
        return $correo->render();
    }

    /** 4 - Cuando esta listo para retirar un pedido */
    public static function pedidoRetirar(Order $pedido)
    {
        $correo = new PedidoRetirarEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new PedidoRetirarEmail($pedido));
        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoEntregado(Order $pedido)
    {
        $correo = new PedidoEntregadoEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new PedidoEntregadoEmail($pedido));
        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoOnlineEntregado(Order $pedido)
    {
        $correo = new PedidoOnlineEntregadoEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new PedidoOnlineEntregadoEmail($pedido));
        return $correo->render();
    }

    /** 6 - Cuando se despacha un envio */
    public static function envioDespachado(Shipment $shipment)
    {
        $correo = new EnvioDespachadoEmail($shipment);
        Mail::to('gon.pineiro@gmail.com')->send(new EnvioDespachadoEmail($shipment));
        return $correo->render();
    }

    /** 7 - Compra a proveedor */
    public static function ordenCompra(PurchaseOrder $pedido)
    {
        $correo = new PurchaseOrderEmail($pedido);
        Mail::to('gon.pineiro@gmail.com')->send(new PurchaseOrderEmail($pedido));
        return $correo->render();
    }
}
