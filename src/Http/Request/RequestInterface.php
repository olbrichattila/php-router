<?php

namespace Aolbrich\PhpRouter\Http\Request;

interface RequestInterface
{
    public function getUri(): string;
    public function getMethod(): string;
}
