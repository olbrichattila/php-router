<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

use Aolbrich\RequestResponse\Http\Response\JsonResponse;
use Aolbrich\RequestResponse\Http\Response\ResponseInterface;

class JsonMiddleware
{
    public function handle(JsonResponse $response): ResponseInterface
    {
        $response->arrayToJson(['middleware' => true]);
        $response->setResponseCode(201);

        return $response;
    }
}
