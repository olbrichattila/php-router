<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;
use Aolbrich\PhpRouter\Http\Response\Response;

class TestAfterMiddleware
{
    public function handle(Response $response): Response
    {
        echo "body 3 " . $response->getBody();
        $response->setBody($response->getBody() . ' +1');
        echo " After Middleware 1<br>";
        return $response;
    }
}
