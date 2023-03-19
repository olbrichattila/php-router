<?php

namespace Aolbrich\PhpRouter\Request;

interface RequestInterface
{
    public function getUri(): string;
    public function getMethod(): string;
}
