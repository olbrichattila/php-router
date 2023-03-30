<?php

namespace Aolbrich\PhpRouter\Http\Request;

interface RequestInterface
{
    public function getUri(): string;
    public function getMethod(): string;
    public function body(): string;
    public function jsonBody(): ?array;
    public function params(): array;
}
