<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;

use Aolbrich\PhpDiContainer\Container;

class Router
{
    private RouterService $routerService;

    public static function getRouter()
    {
        // $container = new Container([
        //     \Aolbrich\PhpRouter\Request\RequestInterface::class => \Aolbrich\PhpRouter\Request\Request::class,
        // ]);

        // return $container->get(RouterService::class);

        // @TODO think over the DI
        return new RouterService(
            new \Aolbrich\PhpRouter\Request\Request(),
            new Container()
        );
    }
}
