<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\PhpRouter\Router;
use Aolbrich\PhpRouter\RouterService;
use PHPUnit\Framework\TestCase;

class RouterLoadsTest extends TestCase
{
    public function testCanBeBuilt(): void
    {
        // Assert should not be an error
        self::assertInstanceOf(RouterService::class, Router::getRouter());
    }
}
