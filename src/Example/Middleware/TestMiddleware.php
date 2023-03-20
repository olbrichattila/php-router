<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;
use Aolbrich\PhpRouter\Http\Response\Response;

class TestMiddleware
{
    public function handle(Response $response): Response
    {
        echo "body 1 " . $response->getBody();
        $response->setBody('Body Set');
        echo " Before Middleware 1<br>";
        return $response;
    }
}
