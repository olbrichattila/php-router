<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example;

use Aolbrich\PhpRouter\Router;
use Aolbrich\RequestResponse\Http\Request\Request;
use Aolbrich\RequestResponse\Http\Response\Response;

require_once realpath(__DIR__ . '/../../vendor') . '/autoload.php';

$router = Router::getRouter();

$router->get('/', function () {
    return "it works";
})->post('/', function () {
    return "it works";
})->get('/api/{id}/edit', function (int $id, Request $request) {
    return "Api works \n" . $id . print_r($request->params(), true);
    
})->get('/api', function () {
    return "Api works";
})->get('/api/controller', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'index'])
->get('/api/controller/{id}/{text}', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'params'])
->middleware([
    \Aolbrich\PhpRouter\Example\Middleware\JsonMiddleware::class
],[], function($router) {
    $router->get('/api/json', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'json']);
});

$router->middleware([
        \Aolbrich\PhpRouter\Example\Middleware\TestMiddleware::class,
        \Aolbrich\PhpRouter\Example\Middleware\Test2Middleware::class
], [
    \Aolbrich\PhpRouter\Example\Middleware\TestAfterMiddleware::class,
    \Aolbrich\PhpRouter\Example\Middleware\Test2AfterMiddleware::class
], function ($router) {
    $router->get('/middleware', function (Response $response) {
        return 'Controller 1<br>' . $response->getBody();
    });
});

$router->get('/validation', [\Aolbrich\PhpRouter\Example\Controller\TestController::class, 'validator']);

$response = $router->run();
$response->render();
