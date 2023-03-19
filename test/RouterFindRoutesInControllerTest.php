<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\Router\Test\MockControllers\MockController;
use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

require_once 'MockControllers/MockController.php';
/**
 * Summary of ContainerTest
 */
class RouterFindRoutesInControllerTest extends TestCase
{
    public function testRouterFindRootGetRoute(): void
    {
        $request = new MockRequest();

        $router = new RouterService($request, new Container());
        $router->get('/', [MockController::class, 'index']);
        $router->run();

        $controller = $router->getController(MockController::class);
        $this->assertNotNull($controller); 
        
        $this->assertEquals(1, $controller->callCount);
    }

    public function testRouterFindRouteAndPassParameters(): void
    {
        $par1 = 50;
        $par2 = "hello";
        $request = new MockRequest();
        $request->setUri('/test/par1/' . $par1 . '/par2/' . $par2);

        $router = new RouterService($request, new Container());
        $router->get('/test/par1/{par1}/par2/{par2}', [MockController::class, 'paramteterTest']);
        $router->run();

        $controller = $router->getController(MockController::class);
        $this->assertNotNull($controller); 
        
        $this->assertEquals($par1, $controller->par1);
        $this->assertEquals($par2, $controller->par2);
    }
}
