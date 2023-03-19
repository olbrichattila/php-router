<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Request;

class MockRequest implements RequestInterface
{
    private string $uri = '/';
    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
