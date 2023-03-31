<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\Router\Test\MockMidlewares\MockMiddleware;
use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Request;
use Aolbrich\PhpRouter\Http\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

require_once 'MockControllers/MockController.php';
require_once 'MockMiddlewares/MockMiddleware.php';

class RouterCallsMiddlewareTest extends TestCase
{
    public function testRouterInvokesMiddleware(): void
    {
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });

        $router = new RouterService($container);

        $router->middleware([
            MockMiddleware::class
        ], [
            MockMiddleware::class
        ], function ($router) {
            $router->get('/', function () {
            });
        });

        $router->run();

        $middlewares = $router->getMiddlewareInstances();

        // Assert only 1 middleware created
        $this->assertCount(1, $middlewares);

        // Handle function called twice
        $this->assertEquals(2, $middlewares[MockMiddleware::class]->callCount);
    }
}
