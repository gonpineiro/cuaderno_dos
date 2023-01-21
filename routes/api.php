<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ApiController;

Route::post('login', [ApiController::class, 'login']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('refresh', [ApiController::class, 'refresh']);
    Route::get('get_user', [ApiController::class, 'get_user']);

    Route::resource('producto', ProductController::class);
});



/* php artisan make:model Api/Product -rcmfsR */
