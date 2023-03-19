<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

class TestMiddleware {
    public function handle(): string
    {
        echo "Before Middleware 1<br>";
        return "4";
    }
}
