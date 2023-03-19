<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Request;

class Request implements RequestInterface
{
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}
