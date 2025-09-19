<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/pdf', function () {
    $cotizacion = (object) [
        'id' => 456,
        'created_at' => now(),
        'client' => (object) [
            'name' => 'Carlos López',
            'is_insurance' => true,
        ],
        'vehiculo' => (object) [
            'name' => 'Renault Kangoo',
        ],
        'version' => '1.6 Confort',
        'patente' => 'AC567FG',
        'type_price' => (object) [
            'value' => 'lista', // o "contado"
        ],
        'contacto' => '299-478-1234',
        'observation' => 'Los precios incluyen IVA. Entrega sujeta a disponibilidad de stock.',
    ];

    $detail = [
        [
            'description' => 'Bujía NGK Iridium',
            'amount' => 4,
            'unit_price' => 5200,
            'total' => 20800,
        ],
        [
            'description' => 'Filtro de aceite',
            'amount' => 1,
            'unit_price' => 3500,
            'total' => 3500,
        ],
        [
            'description' => 'Kit de distribución',
            'amount' => 1,
            'unit_price' => 45000,
            'total' => 45000,
        ],
    ];

    $is_contado = $cotizacion->type_price->value === 'contado';
    $total = collect($detail)->sum('total');

    $vars = [
        'cotizacion' => $cotizacion,
        'detail' => $detail,
        'coefs' => ['coef1' => 1.2, 'coef2' => 1.5], // mock
        'total' => number_format($total, 0, ',', '.'),
        'type' => 'externo', // o 'interno'
        'is_contado' => $is_contado,
    ];

    $pdf = Pdf::loadView('pdf.cotizaciones.total', $vars);

    return $pdf->download('cotizacion-prueba.pdf');
});
