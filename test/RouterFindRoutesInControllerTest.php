<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\Router\Test\MockControllers\MockController;
use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Request;
use Aolbrich\PhpRouter\Http\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

require_once 'MockControllers/MockController.php';

class RouterFindRoutesInControllerTest extends TestCase
{
    public function testRouterFindRootGetRoute(): void
    {
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });
        $request = $container->get(Request::class);
        $router = new RouterService($container);

        $router->get('/', [MockController::class, 'index']);
        $router->run();

        $controller = $router->getController(MockController::class);
        $this->assertNotNull($controller);

        $this->assertEquals(1, $controller->callCount);

        $otherMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        foreach ($otherMethods as $otherMethod) {
            $methodFunctionName = strtolower($otherMethod);
            $request->setMethod($otherMethod);
            $router->$methodFunctionName('/', [MockController::class, $methodFunctionName]);
            $router->run();
            $countVarableName = $methodFunctionName . 'Count';

            $this->assertEquals(1, $controller->$countVarableName, $countVarableName);
        }
    }

    public function testRouterFindRouteAndPassParameters(): void
    {
        $par1 = 50;
        $par2 = "hello";
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });
        $request = $container->get(Request::class);
        $request->setUri('/test/par1/' . $par1 . '/par2/' . $par2);
        $router = new RouterService($container);

        $router->get('/test/par1/{par1}/par2/{par2}', [MockController::class, 'paramteterTest']);
        $router->run();

        $controller = $router->getController(MockController::class);
        $this->assertNotNull($controller);

        $this->assertEquals($par1, $controller->par1);
        $this->assertEquals($par2, $controller->par2);
    }
}
