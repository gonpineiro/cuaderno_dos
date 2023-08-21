<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceQuoteController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CityController;

Route::post('login', [ApiController::class, 'login']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('refresh', [ApiController::class, 'refresh']);
    Route::get('get_user', [ApiController::class, 'get_user']);

    Route::resource('user', UserController::class);
    Route::resource('proveedor', ProviderController::class);
    Route::resource('ciudad', CityController::class);
    Route::resource('cliente', ClientController::class);
    Route::get('producto/relacion', [ProductController::class, 'relation']);
    Route::get('producto/relacion/sin-stock', [ProductController::class, 'relationEmptyStock']);
    /* Route::get('producto/pedido-online', [ProductController::class, 'inPedidoOnline']); */
    Route::post('producto/guardar-fuera-catalogo', [ProductController::class, 'storeOutCatalogue']);
    Route::post('producto/guardar-unico', [ProductController::class, 'storeIsSpecial']);
    Route::resource('producto', ProductController::class)->except(['destroy']);

    Route::get('producto/{id}/pedidos', [ProductController::class, 'pedidos']);
    Route::get('producto/{id}/cotizaciones', [ProductController::class, 'cotizaciones']);

    Route::get('pedido/reporte-online', [OrderController::class, 'getReportePedidosOnline']);
    Route::post('pedido/cambiar-estado/{id}', [OrderController::class, 'updateStateCliente']);
    Route::resource('pedido', OrderController::class)->only(['update', 'destroy']);

    /* Clientes */
    Route::get('pedido', [OrderController::class, 'indexPedidosCliente']);
    Route::get('pedido/{id}', [OrderController::class, 'showPedidoCliente']);

    /* Siniestros */
    Route::get('siniestro', [OrderController::class, 'indexSiniestros']);
    Route::get('siniestro/{id}', [OrderController::class, 'showSiniestro']);
    Route::post('siniestro/cambiar-estado/{id}', [OrderController::class, 'updateStateSiniestro']);

    /* Pedidos Online */
    Route::get('online', [OrderController::class, 'indexOnlines']);
    Route::get('online/{id}', [OrderController::class, 'showPedidoOnline']);
    Route::post('online/cambiar-estado/{id}', [OrderController::class, 'updateStateOnline']);

    /* Pedidos Online */
    Route::get('envio', [OrderController::class, 'indexEnvios']);
    Route::get('envio/{id}', [OrderController::class, 'showPedidoOnline']);
    Route::post('envio/cambiar-estado/{id}', [OrderController::class, 'updateStateEnvio']);

    /* Cotizaciones */
    Route::resource('cotizacion', PriceQuoteController::class);
    Route::post('cotizacion/asignar/siniestro', [PriceQuoteController::class, 'asignarSiniestro']);
    Route::post('cotizacion/asignar/online', [PriceQuoteController::class, 'asignarOnline']);
    Route::post('cotizacion/asignar/cliente', [PriceQuoteController::class, 'asignarCliente']);
    Route::post('cotizacion/asignar/envio', [PriceQuoteController::class, 'asignarEnvio']);
    Route::post('update_price_quote_product', [PriceQuoteController::class, 'update_price_quote_product']);

    Route::get('sendEmail', [OrderController::class, 'enviarCorreo']);

    Route::post('update_pedido_product', [OrderProductController::class, 'updateCliente']);
    Route::post('update_siniestro_product', [OrderProductController::class, 'updateSiniestro']);
    Route::post('update_envio_product', [OrderProductController::class, 'updateEnvio']);
});

Route::get('pedido/pdf/{id}', [OrderController::class, 'getPdfPedido']);

Route::get('cotizacion/pdf/{id}', [PriceQuoteController::class, 'getPdf']);



/* php artisan make:model Api/Product -rcmfsR */
