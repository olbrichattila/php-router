<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;
use Aolbrich\PhpRouter\Http\Response\Response;

class Test2AfterMiddleware
{
    public function handle(Response $response): Response
    {
        echo "body 4 " . $response->getBody();
        echo " After Middleware 2<br>";

        return $response;
    }
}
