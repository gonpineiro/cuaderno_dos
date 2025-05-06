<?php

namespace App\Http\TraitsControllers;

use App\Mail\PedidoCancelado;
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
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new CrearPedidoProductoUnicoEmail($pedido);
        Mail::to($email)->send(new CrearPedidoProductoUnicoEmail($pedido));
        return $correo->render();
    }

    /** 2 - Cuando esta listo para retirar un pedido con producto unico  */
    public static function pedidoUnicoRetirar(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new PedidoUnicoRetirarEmail($pedido);
        Mail::to($email)->send(new PedidoUnicoRetirarEmail($pedido));
        return $correo->render();
    }

    /** 4 - Cuando esta listo para retirar un pedido */
    public static function pedidoRetirar(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new PedidoRetirarEmail($pedido);
        Mail::to($email)->send(new PedidoRetirarEmail($pedido));
        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoEntregado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new PedidoEntregadoEmail($pedido);
        Mail::to($email)->send(new PedidoEntregadoEmail($pedido));
        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoOnlineEntregado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new PedidoOnlineEntregadoEmail($pedido);
        Mail::to($email)->send(new PedidoOnlineEntregadoEmail($pedido));
        return $correo->render();
    }

    /** 6 - Cuando se despacha un envio */
    public static function envioDespachado(Shipment $shipment)
    {
        $email = app()->environment() === 'production' ? $shipment->client->email : 'gon.pineiro@gmail.com';
        $correo = new EnvioDespachadoEmail($shipment);
        Mail::to($email)->send(new EnvioDespachadoEmail($shipment));
        return $correo->render();
    }

    /** 7 - Compra a proveedor */
    public static function ordenCompra(PurchaseOrder $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->provider->email : 'gon.pineiro@gmail.com';
        $correo = new PurchaseOrderEmail($pedido);
        Mail::to($email)->send(new PurchaseOrderEmail($pedido));
        return $correo->render();
    }

    public static function pedidoCancelado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'gon.pineiro@gmail.com';
        $correo = new PedidoCancelado($pedido);
        Mail::to( $email)->send(new PedidoCancelado($pedido));
        return $correo->render();
    }
}
