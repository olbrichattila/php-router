<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Request;

// these requests may go to another library, and will be imported, injected
class Request implements RequestInterface
{
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}
