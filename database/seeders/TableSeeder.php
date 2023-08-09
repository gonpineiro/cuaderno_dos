<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'FIAT']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'RENO']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'CITROEN']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'BMW']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'ALGO']);

        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'online', 'description' => 'Pedido Online']);
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'cliente', 'description' => 'Pedido Cliente']);
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'siniestro', 'description' => 'Pedido Siniestro']);
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'envio', 'description' => 'Envío']);

        DB::table('tables')->insert(['name' => 'order_online_state', 'value' => 'pendiente', 'description' => 'Pendiente', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_online_state', 'value' => 'retirar', 'description' => 'Retirar', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_online_state', 'value' => 'entregado', 'description' => 'Entregado', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_online_state', 'value' => 'cancelado', 'description' => 'Cancelado', 'background_color' => '#6c757d', 'color' => 'black']);

        DB::table('tables')->insert(['name' => 'order_cliente_state', 'value' => 'pendiente', 'description' => 'Pendiente', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_cliente_state', 'value' => 'recibido', 'description' => 'Recibido', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_cliente_state', 'value' => 'avisado', 'description' => 'Avisado', 'background_color' => '#0d6efd', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_cliente_state', 'value' => 'entregado', 'description' => 'Entregado', 'background_color' => '#198754', 'color' => 'white']);

        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'incompleto', 'description' => 'Incompleto', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'entregado', 'description' => 'Entregado', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'completo', 'description' => 'Completo', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'cancelado', 'description' => 'Cancelado', 'background_color' => '#6c757d', 'color' => 'black']);

        DB::table('tables')->insert(['name' => 'order_envio_state', 'value' => 'pendiente', 'description' => 'Pendiente a preparar', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_envio_state', 'value' => 'listo_enviar', 'description' => 'Listo para enviar', 'background_color' => '#0d6efd', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_envio_state', 'value' => 'despachado', 'description' => 'Despachado', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_envio_state', 'value' => 'contrareemboldo', 'description' => 'Contrareemboso pago', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_envio_state', 'value' => 'cancelado', 'description' => 'Cancelado', 'background_color' => '#6c757d', 'color' => 'black']);

        DB::table('tables')->insert(['name' => 'price_quote_state', 'value' => 'cotizar', 'description' => 'Cotizar', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'price_quote_state', 'value' => 'no cotizar', 'description' => 'No Cotizar', 'background_color' => '#6c757d', 'color' => 'black']);

        DB::table('tables')->insert(['name' => 'config', 'value' => 'product.ship', 'description' => '1']);

        DB::table('tables')->insert(['name' => 'type_price', 'value' => 'contado', 'description' => 'Precio de contado']);
        DB::table('tables')->insert(['name' => 'type_price', 'value' => 'lista', 'description' => 'Precio de lista']);

        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'wap-clientes', 'description' => 'WhatsApp Clientes']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'wap-mecanicos', 'description' => 'WhatsApp Mecanicos']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'facebook', 'description' => 'Facebook']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'Mostrador', 'description' => 'Mostrador']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'email', 'description' => 'Mail']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'google', 'description' => 'Google']);
        DB::table('tables')->insert(['name' => 'information_source', 'value' => 'pagina-web', 'description' => 'Pagina Web']);

        DB::table('tables')->insert(['name' => 'payment_method', 'value' => 'mostrador', 'description' => 'Paga en mostrador']);
        DB::table('tables')->insert(['name' => 'payment_method', 'value' => 'online', 'description' => 'Pagado online']);
        DB::table('tables')->insert(['name' => 'payment_method', 'value' => 'corriente', 'description' => 'Cuenta corriente']);

        DB::table('tables')->insert(['name' => 'payment_method_send', 'value' => 'contrareembolso', 'description' => 'Contrareembolso']);
        DB::table('tables')->insert(['name' => 'payment_method_send', 'value' => 'online', 'description' => 'Pagado online']);
        DB::table('tables')->insert(['name' => 'payment_method_send', 'value' => 'corriente', 'description' => 'Cuenta corriente']);
    }
}
