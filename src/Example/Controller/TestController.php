<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Example\Controller;

class TestController
{
    public function index()
    {
        echo "works";
    }

    public function params(string $text, int $id): void
    {
        echo $text . '<br>';
        echo $id . '<br>';
        echo 'Works';
    }
}
