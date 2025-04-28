<?php
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
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

//// KYNAR NETWORK LARAVEL INSTALLER START ////
function loadEnv() {
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) {
        return [];
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVariables = [];

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos(trim($line), '=') === false) continue; // Skip comments and invalid lines
        list($name, $value) = explode('=', $line, 2);
        $envVariables[trim($name)] = trim($value, '"\'');
    }

    return $envVariables;
}

function env($key, $default = null) {
    static $envVariables = [];
    if (empty($envVariables)) {
        $envVariables = loadEnv();
    }
    return isset($envVariables[$key]) ? $envVariables[$key] : $default;
}


$envPath = '../.env';

$dbConfigured = file_exists($envPath) && env('DB_CONNECTION') !== false;
$appKeySet = env('APP_KEY') !== null;

if (!$dbConfigured || !$appKeySet) {
    header("Location: /setup/index.php");
    exit;
}

//// KYNAR NETWORK LARAVEL INSTALLER END ////


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
require __DIR__.'/../vendor/autoload.php';
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
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);