<?php

use App\Http\Controllers\ClientChasisController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductJazzController;

use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceQuoteController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientConfigController;
use App\Http\Controllers\CoeficienteController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\JazzController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\VehiculoController;

Route::post('login', [ApiController::class, 'login']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('refresh', [ApiController::class, 'refresh']);
    Route::get('get_user', [ApiController::class, 'get_user']);

    Route::resource('user', UserController::class);
    Route::resource('proveedor', ProviderController::class);
    Route::post('ciudad/buscar', [CityController::class, 'search']);
    Route::resource('ciudad', CityController::class);
    Route::resource('vehiculo', VehiculoController::class);

    /* Clientes */
    Route::get('cliente/referencia', [ClientController::class, 'getByReference']);
    Route::post('cliente/buscar', [ClientController::class, 'search']);
    Route::post('cliente/update', [ClientController::class, 'update']);
    Route::post('cliente/config/save', [ClientConfigController::class, 'saveConfig']);
    Route::resource('cliente', ClientController::class)->except(['show', 'update']);


    /* Cliente Chasis */
    Route::post('cliente_chasis/update', [ClientChasisController::class, 'cliente_chasis_update']);
    Route::get('chasis', [ClientChasisController::class, 'index']);

    Route::resource('marca', BrandController::class);

    Route::post('product_marca/borrar', [ProductBrandController::class, 'delete']);
    Route::post('product_marca/update', [ProductBrandController::class, 'update']);
    Route::resource('product_marca', ProductBrandController::class)->except(['update', 'delete']);

    Route::get('producto/buscar', [ProductController::class, 'search']);
    Route::get('producto/detalle_jazz', [ProductJazzController::class, 'detalle']);
    Route::get('producto/jazz', [ProductJazzController::class, 'index']);

    Route::post('producto/borrar', [ProductController::class, 'delete']);
    Route::post('producto/recuperar', [ProductController::class, 'recuperarProducto']);

    Route::get('producto/relacion', [ProductController::class, 'relation']);
    Route::get('producto/cotizaciones', [ProductController::class, 'getInCotizaciones']);

    Route::get('producto/relacion/sin-stock', [ProductController::class, 'relationEmptyStock']);
    /* Route::get('producto/pedido-online', [ProductController::class, 'inPedidoOnline']); */
    /* Route::post('producto/guardar-simple', [ProductController::class, 'storeIsSimple']); */
    /* Route::post('producto/guardar-unico', [ProductController::class, 'storeIsSpecial']); */

    Route::get('producto/audit', [ProductController::class, 'audit'])->middleware('permission:audit.product.view');

    Route::resource('producto', ProductController::class)->except(['destroy']);

    Route::get('producto/{id}/pedidos', [ProductController::class, 'pedidos']);
    Route::get('producto/{id}/cotizaciones', [ProductController::class, 'cotizaciones']);

    Route::get('pedido/reporte-online', [OrderController::class, 'getReportePedidosOnline']);
    Route::post('pedido/borrar', [OrderController::class, 'destroy']);
    Route::resource('pedido', OrderController::class)->only(['update', 'destroy']);

    /* Clientes */
    /* Route::get('pedido', [OrderController::class, 'indexPedidosCliente']);
    Route::get('pedido/{id}', [OrderController::class, 'showPedidoCliente']);
    Route::post('pedido/cambiar-estado/{id}', [OrderController::class, 'updateStateCliente']);
    Route::post('update_pedido_product', [OrderProductController::class, 'updateCliente']); */

    /* Clientes */
    Route::get('pedidos', [OrderController::class, 'index']);
    Route::get('pedidos/{id}', [OrderController::class, 'showPedido']);

    Route::post('pedido/search', [OrderController::class, 'search']);
    Route::get('pedido/productos', [OrderController::class, 'productos']);
    Route::post('pedido/productos/search', [OrderController::class, 'productos_search']);
    Route::put('pedidos/{id}', [OrderController::class, 'updatePedido']);
    Route::get('pedido/pdf/{id}', [OrderController::class, 'getPdfPedido']);
    Route::post('pedidos/cambiar-estado', [OrderController::class, 'updateState']);
    Route::post('update_pedido_product', [OrderProductController::class, 'updatePedidoProduct']);

    /* Siniestros */
    Route::get('siniestro', [OrderController::class, 'indexSiniestros']);
    Route::get('siniestro/{id}', [OrderController::class, 'showSiniestro']);
    Route::post('siniestro/cambiar-estado/{id}', [OrderController::class, 'updateStateSiniestro']);
    Route::post('update_siniestro_product', [OrderProductController::class, 'updateSiniestro']);

    /* Pedidos Online */
    Route::get('online', [OrderController::class, 'indexOnlines']);
    Route::get('online/{id}', [OrderController::class, 'showPedidoOnline']);
    Route::post('online/cambiar-estado/{id}', [OrderController::class, 'updateStateOnline']);

    /* Envios */
    Route::resource('envio', ShipmentController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::put('envio/{id}', [ShipmentController::class, 'updateEnvio']);
    Route::put('envio/{id}/update-productos', [ShipmentController::class, 'update']);
    Route::post('envio/cambiar-estado', [ShipmentController::class, 'updateState']);
    Route::get('envio/pdf/{id}', [ShipmentController::class, 'get_pdf']);
    Route::post('update_envio_product', [ShipmentController::class, 'update_envio_product']);

    /* Cotizaciones */
    Route::resource('cotizacion', PriceQuoteController::class)->only(['index', 'store', 'show']);
    Route::put('cotizacion/{id}', [PriceQuoteController::class, 'updateCotizacion']);
    Route::post('cotizacion/borrar', [PriceQuoteController::class, 'destroy']);
    Route::post('cotizacion/search', [PriceQuoteController::class, 'search']);
    Route::put('cotizacion/{id}/update-productos', [PriceQuoteController::class, 'update']);

    Route::post('cotizacion/asignar/siniestro', [PriceQuoteController::class, 'asignarSiniestro']);
    Route::post('cotizacion/asignar/online', [PriceQuoteController::class, 'asignarOnline']);
    Route::post('cotizacion/asignar/cliente', [PriceQuoteController::class, 'asignarCliente']);

    Route::post('cotizacion/asignar/envio', [PriceQuoteController::class, 'asignarEnvio']);
    Route::get('cotizacion/pdf/{id}', [PriceQuoteController::class, 'getPdf']);
    Route::post('update_price_quote_product', [PriceQuoteController::class, 'update_price_quote_product']);

    /* Ordenes de compra */
    Route::post('ordenes_compra/generar_pedir', [PurchaseOrderController::class, 'generar_pedir']);
    Route::post('ordenes_compra/producto_generar_pedir', [PurchaseOrderController::class, 'producto_generar_pedir']);
    Route::post('ordenes_compra/producto_modificar_pedir', [PurchaseOrderController::class, 'producto_modificar_pedir']);
    Route::get('ordenes_compra/pedir', [PurchaseOrderController::class, 'pedir']);
    Route::post('ordenes_compra/borrar', [PurchaseOrderController::class, 'delete']);
    Route::post('generar_orden/generar', [PurchaseOrderController::class, 'generar_orden']);
    Route::post('ordenes_compra/cambiar-estado/{id}', [PurchaseOrderController::class, 'update']);
    Route::resource('ordenes_compra', PurchaseOrderController::class);

    /* Combs */
    Route::get('combos', [ComboController::class, 'index']);
    Route::post('combos/borrar', [ComboController::class, 'destroy']);
    Route::post('combos', [ComboController::class, 'store']);
    Route::post('combos/update', [ComboController::class, 'update']);

    /* Coeficientes */
    Route::post('coeficientes/update', [CoeficienteController::class, 'store']);



    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permissions/change_user_role', [PermissionController::class, 'change_user_role']);
    Route::post('permissions/change_user_permissions', [PermissionController::class, 'change_user_permissions']);
    Route::post('permissions/save_element', [PermissionController::class, 'save_element']);
    Route::post('permissions/change_role_permission', [PermissionController::class, 'change_role_permission']);


    Route::get('jazz/get_stock_product', [JazzController::class, 'get_stock_product']);
});

Route::get('producto/jazz/inicio-sync', [JazzController::class, 'syncProductTemp']);
Route::get('producto/jazz/analizar', [JazzController::class, 'procesarTemporal']);
Route::post('producto/jazz/sincronizar', [JazzController::class, 'sync']);
Route::get('products_jazz', [JazzController::class, 'getProducts']);
Route::get('generar_pedido', [JazzController::class, 'generarPedidoApi']);
