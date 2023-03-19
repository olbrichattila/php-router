<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Request\Request;

class Router
{
    private RouterService $routerService;

    public static function getRouter()
    {
        // $container = new Container([
        //     \Aolbrich\PhpRouter\Request\RequestInterface::class => \Aolbrich\PhpRouter\Request\Request::class,
        // ]);

        // return $container->get(RouterService::class);

        // @TODO think it over how to do the DI here, should use the APP di what we don't have here,
        // or at least to be able to use the settings
        return new RouterService(
            new Request(),
            new Container()
        );
    }
}
