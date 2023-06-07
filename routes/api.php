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
    Route::resource('producto', ProductController::class);

    Route::get('orden/reporte-online', [OrderController::class, 'getReportePedidosOnline']);
    Route::post('orden/cambiar-estado/{id}', [OrderController::class, 'updateState']);
    Route::resource('orden', OrderController::class);

    Route::resource('cotizacion', PriceQuoteController::class);

    Route::get('sendEmail', [OrderController::class, 'enviarCorreo']);

    Route::post('update_order_product', [OrderProductController::class, 'update']);
});
Route::get('orden/pdf/{id}', [OrderController::class, 'getPdfPedido']);



/* php artisan make:model Api/Product -rcmfsR */
