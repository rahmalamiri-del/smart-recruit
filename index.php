<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

$autoload = __DIR__.'/vendor/autoload.php';

if (! file_exists($autoload)) {
    http_response_code(500);
    exit('Smart-Recruit dependencies are missing. Run composer install inside smart-recruit.');
}

$loader = require $autoload;

if (method_exists($loader, 'addPsr4')) {
    $loader->addPsr4('SmartRecruit\\', __DIR__.'/app/');
}

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
