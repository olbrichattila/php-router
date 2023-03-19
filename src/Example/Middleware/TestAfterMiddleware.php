<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

class TestAfterMiddleware
{
    public function handle(): int
    {
        echo "After Middleware 1<br>";
        return 3;
    }
}
