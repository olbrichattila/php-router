<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example;

use Aolbrich\PhpRouter\MiddlewareAdapder;
use Aolbrich\PhpRouter\Router;

require_once realpath(__DIR__ . '/../../vendor') . '/autoload.php';

$router = Router::getRouter();

$router->get('/', function () {
    echo "it works";
})->get('/api/{id}/edit', function (int $id) {
    echo "Api works " . $id;
})->get('/api', function () {
    echo "Api works";
})->get('/api/controller', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'index'])
->get('/api/controller/{id}/{text}', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'params']);

$router->middleware([
        \Aolbrich\PhpRouter\Example\Middleware\TestMiddleware::class,
        \Aolbrich\PhpRouter\Example\Middleware\Test2Middleware::class
], [
    \Aolbrich\PhpRouter\Example\Middleware\TestAfterMiddleware::class,
    \Aolbrich\PhpRouter\Example\Middleware\Test2AfterMiddleware::class
], function ($router) {
    $router->get('/middleware', function () {
        echo 'Controller 1<br>';
    });
});


$router->run();
