<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

class Test2AfterMiddleware {
    public function handle(): string
    {
        echo "After Middleware 2<br>";

        return "1";
    }
}
