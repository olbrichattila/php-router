<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Controller;
use Aolbrich\PhpRouter\Http\Response\JsonResponse;
use Aolbrich\PhpRouter\Http\Response\ResponseInterface;

class TestController
{
    public function index(): string
    {
        return "works";
    }

    public function params(string $text, int $id): string
    {
        return
            $text . '<br>' .
            $id . '<br>' .
             'Works';
    }

    public function json(JsonResponse $response): ResponseInterface
    {
        $response->mergeToJson(['status' => 'OK']);

        return $response;
    }
}
