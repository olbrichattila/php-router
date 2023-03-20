<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;

use Aolbrich\PhpDiContainer\Container;

class Router
{
    private RouterService $routerService;

    public static function getRouter(Container $container = null)
    {
        $container = $container ?? new Container();
        return new RouterService(
            $container
        );
    }
}
