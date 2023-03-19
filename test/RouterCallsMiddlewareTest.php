<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\Router\Test\MockMidlewares\MockMiddleware;
use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

require_once 'MockControllers/MockController.php';
require_once 'MockMiddlewares/MockMiddleware.php';
/**
 * Summary of ContainerTest
 */
class RouterCallsMiddlewareTest extends TestCase
{
    public function testRouterInvokesMiddleware(): void
    {
        $request = new MockRequest();

        $router = new RouterService($request, new Container());
        $router->middleware([
            MockMiddleware::class
        ],[
            MockMiddleware::class
        ], function($router) {
            $router->get('/', function() {});
        });
        $middlewareResults = $router->run();

        $middlewares = $router->getMiddlewareInstances();

        // Assert only 1 middleware created
        $this->assertCount(1, $middlewares);

        // Handle function called twice
        $this->assertEquals(2, $middlewares[MockMiddleware::class]->callCount);

        // There are two middleware results
        $this->assertCount(2, $middlewareResults);
    }
}
