<?php

namespace App\Http\TraitsControllers;

use App\Mail\PedidoCancelado;
use Illuminate\Support\Facades\Mail;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\EmailLog;

use App\Mail\CrearPedidoProductoUnicoEmail;
use App\Mail\EnvioDespachadoEmail;
use App\Mail\PurchaseOrderEmail;
use App\Mail\PedidoRetirarEmail;
use App\Mail\PedidoEntregadoEmail;
use App\Mail\PedidoOnlineEntregadoEmail;
use App\Mail\PedidoRetirarVencido;
use App\Mail\PedidoUnicoRetirarEmail;
use App\Models\PurchaseOrder;

/* Nicolasallende90@gmail.com */

trait TraitPedidosEmail
{
    /** 1 - Cuando se crea un pedido con un producto unico */
    public static function pedidoProductoUnico(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new CrearPedidoProductoUnicoEmail($pedido);

        try {
            Mail::to($email)->send(new CrearPedidoProductoUnicoEmail($pedido));
            EmailLog::create([
                'type' => 'pedido_producto_unico',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Nuevo pedido creado',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_producto_unico',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 2 - Cuando esta listo para retirar un pedido con producto unico  */
    public static function pedidoUnicoRetirar(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new PedidoUnicoRetirarEmail($pedido);

        try {
            Mail::to($email)->send(new PedidoUnicoRetirarEmail($pedido));
            EmailLog::create([
                'type' => 'pedido_unico_retirar',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido listo para retirar',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_unico_retirar',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 4 - Cuando esta listo para retirar un pedido */
    public static function pedidoRetirar(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new PedidoRetirarEmail($pedido);

        try {
            Mail::to($email)->send(new PedidoRetirarEmail($pedido));
            EmailLog::create([
                'type' => 'pedido_retirar',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido listo para retirar',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_retirar',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoEntregado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new PedidoEntregadoEmail($pedido);

        try {
            Mail::to($email)->send(new PedidoEntregadoEmail($pedido));
            EmailLog::create([
                'type' => 'pedido_entregado',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido entregado',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_entregado',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 5 - Cuando se entrega un pedido */
    public static function pedidoOnlineEntregado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new PedidoOnlineEntregadoEmail($pedido);

        try {
            Mail::to($email)->send(new PedidoOnlineEntregadoEmail($pedido));
            EmailLog::create([
                'type' => 'pedido_online_entregado',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido entregado',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_online_entregado',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 6 - Cuando se despacha un envio */
    public static function envioDespachado(Shipment $shipment)
    {
        $email = app()->environment() === 'production' ? $shipment->client->email : 'Nicolasallende90@gmail.com';
        $correo = new EnvioDespachadoEmail($shipment);

        try {
            Mail::to($email)->send(new EnvioDespachadoEmail($shipment));
            EmailLog::create([
                'type' => 'envio_despachado',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Envío despachado',
                'entity_type' => 'shipment',
                'entity_id' => $shipment->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'envio_despachado',
                'to' => $email,
                'entity_type' => 'shipment',
                'entity_id' => $shipment->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    /** 7 - Compra a proveedor */
    public static function ordenCompra(PurchaseOrder $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->provider->email : 'Nicolasallende90@gmail.com';
        $correo = new PurchaseOrderEmail($pedido);

        try {
            Mail::to($email)->send(new PurchaseOrderEmail($pedido));
            EmailLog::create([
                'type' => 'orden_compra',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Orden de compra',
                'entity_type' => 'purchase_order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'orden_compra',
                'to' => $email,
                'entity_type' => 'purchase_order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    public static function pedidoCancelado(Order $pedido)
    {
        $email = app()->environment() === 'production' ? $pedido->client->email : 'Nicolasallende90@gmail.com';
        $correo = new PedidoCancelado($pedido);

        try {
            Mail::to($email)->send(new PedidoCancelado($pedido));
            EmailLog::create([
                'type' => 'pedido_cancelado',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido cancelado',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_cancelado',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }

    public static function pedidoRetirarVencido(Order $pedido)
    {
        $email = 'gon.pineiro@gmail.com';
        $correo = new PedidoRetirarVencido($pedido);

        try {
            Mail::to($email)->send(new PedidoRetirarVencido($pedido));
            EmailLog::create([
                'type' => 'pedido_retirar_vencido',
                'to' => $email,
                'subject' => $correo->build()->subject ?? 'Pedido para retirar vencido',
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'type' => 'pedido_retirar_vencido',
                'to' => $email,
                'entity_type' => 'order',
                'entity_id' => $pedido->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $correo->render();
    }
}