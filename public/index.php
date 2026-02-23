<?php

ini_set('memory_limit', '512M');

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

if (isset($_GET['clear_cache']) && $_GET['clear_cache'] == 1) {

    // Bootstrap mínimo
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Cache general
    app('cache')->clear();

    // Archivos cacheados
    @unlink(base_path('bootstrap/cache/config.php'));
    @unlink(base_path('bootstrap/cache/services.php'));
    @unlink(base_path('bootstrap/cache/packages.php'));
    @unlink(base_path('bootstrap/cache/routes.php'));
    @unlink(base_path('bootstrap/cache/routes-v7.php'));

    // Views compiladas
    foreach (glob(storage_path('framework/views/*.php')) as $file) {
        @unlink($file);
    }

    echo '✅ Cache de Laravel limpiado correctamente';
    exit;
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
