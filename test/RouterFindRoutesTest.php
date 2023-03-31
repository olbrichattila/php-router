<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Request;
use Aolbrich\PhpRouter\Http\Request\MockRequest;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

class RouterFindRoutesTest extends TestCase
{
    public function testRouterFindRootGetRoute(): void
    {
        $isCalled = null;
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });

        $router = new RouterService($container);
        $router->get('/', function () use (&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }

    public function testRouterFindRootGetRouteWithParameters(): void
    {
        $isCalled = null;
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });
        $request = $container->get(Request::class);
        $request->setUri('/api/foo/5/edit/50/id');

        $router = new RouterService($container);
        $router->get('/api/foo/{id}/edit/{customer}/id', function ($id, $customer) use (&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }

    public function testRouterFindRootGetRouteWithParametersAndCustomParameter(): void
    {
        $isCalled = null;
        $container = new Container();
        $container->set(Request::class, function ($container) {
            return $container->singleton(MockRequest::class);
        });
        $request = $container->get(Request::class);
        $request->setUri('/api/foo/5/edit/50/id?test=true');

        $router = new RouterService($container);
        $router->get('/api/foo/{id}/edit/{customer}/id', function () use (&$isCalled) {
            $isCalled = true;
        });
        $router->run();

        $this->assertTrue($isCalled);
    }
}
