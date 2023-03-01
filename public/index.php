<?php

use App\Service\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    $router = new Router($requestUri, $requestMethod);
    $router->execute();
} catch (\Throwable $e) {
    print_r($e); // mine 500 выдавать
}
