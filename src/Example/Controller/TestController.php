<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Controller;

use Aolbrich\RequestResponse\Http\Request\Request;
use Aolbrich\RequestResponse\Http\Response\JsonResponse;
use Aolbrich\RequestResponse\Http\Response\ResponseInterface;

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


    public function validator(Request $request, JsonResponse $response): ResponseInterface
    {
        $validated = $request->validate([
            'par1' => 'required|min:10|max:20',
            'par2' => 'required|min-length:2|max-length:4',
            'par3' => 'regex:/^[0-9]+$/',
            'par4' => function($value) {
                return "'{$value}' validation callback";
            },
            'par5' => function($value) {
                return null;
            }
        ]);

        $response->arrayToJson(
            [
                'validated' => $validated,
                'errors' => $request->validationErrors(),
            ]
        );

        return $response;
    }
}
