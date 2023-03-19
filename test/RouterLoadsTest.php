<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test;

use Aolbrich\PhpRouter\Router;
use Aolbrich\PhpRouter\RouterService;

use PHPUnit\Framework\TestCase;

/**
 * Summary of ContainerTest
 */
class RouterLoadsTest extends TestCase
{
    /**
     * Summary of testCanBeBuilt
     * @return void
     */
    public function testCanBeBuilt(): void
    {
        // Assert should not be an error
        self::assertInstanceOf(RouterService::class, Router::getRouter());
    }
}
