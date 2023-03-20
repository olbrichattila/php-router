<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Middleware;

use Aolbrich\PhpRouter\Http\Response\JsonResponse;
use Aolbrich\PhpRouter\Http\Response\ResponseInterface;

class JsonMiddleware
{
    public function handle(JsonResponse $response): ResponseInterface
    {
        $response->arrayToJson(['middleware' => true]);
        $response->setResponseCode(201);

        return $response;
    }
}
