<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));
// Determine if the application is in maintenance mode...
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$headers = getallheaders(); // native PHP
$body = file_get_contents('php://input');

$log = '[' . date('Y-m-d H:i:s') . '] ' . $method . ' ' . $uri . PHP_EOL;
$log .= 'Headers: ' . json_encode($headers) . PHP_EOL;
$log .= 'Body: ' . $body . PHP_EOL . str_repeat('-', 80) . PHP_EOL;

// Simpan ke log file
file_put_contents(
    __DIR__ . '/../storage/logs/request_entry.log',
    $log,
    FILE_APPEND
);
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
