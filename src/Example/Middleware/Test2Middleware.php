<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

class Test2Middleware {
    public function handle(): void
    {
        echo "Before Middleware 2<br>";
    }
}
