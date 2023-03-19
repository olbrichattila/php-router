<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

/**
 * Summary of ContainerTest
 */
class RouterFindRoutesTest extends TestCase
{
    /**
     * Summary of testCanBeBuilt
     * @return void
     */
    public function testRouterFindRootGetRoute(): void
    {
        $isCalled = null;
        $request = new MockRequest();

        $router = new RouterService($request, new Container());
        $router->get('/', function() use(&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }

    public function testRouterFindRootGetRouteWithParameters(): void
    {
        $isCalled = null;
        $request = new MockRequest();
        $request->setUri('/api/foo/5/edit/50/id');

        $router = new RouterService($request, new Container());
        $router->get('/api/foo/{id}/edit/{customer}/id', function($id, $customer) use(&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }

    public function testRouterFindRootGetRouteWithParametersAndCustomParameter(): void
    {
        $isCalled = null;
        $request = new MockRequest();
        $request->setUri('/api/foo/5/edit/50/id?test=true');

        $router = new RouterService($request, new Container());
        $router->get('/api/foo/{id}/edit/{customer}/id', function() use(&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }
}
