<?php

namespace Aolbrich\PhpRouter\Http\Response;

interface ResponseInterface
{
    public function setResponseCode(int $code): void;
    public function getResponseCode(): int;
    public function setHeader(string $key, string $value);
    public function setBody(string $body): void;
    public function getBody(): string;
    public function render(): void;
}
