<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

use Aolbrich\RequestResponse\Http\Response\Response;

class Test2Middleware
{
    public function handle(Response $response): Response
    {
        echo "body 2 " . $response->getBody();
        $response->setBody($response->getBody() . ' + 1');
        echo " Before Middleware 2<br>";

        return $response;
    }
}
