<?php
// @todo create a MOCK header class on namespace

namespace Aolbrich\PhpRouter\Http\Response;

class Response implements ResponseInterface
{
    protected int $code = 200;
    protected array $headers = [];
    protected string $body = '';

    public function setResponseCode(int $code): void
    {
        $this->code = $code;
        http_response_code($code);
    }

    public function getResponseCode(): int
    {
        return $this->code;
    }

    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    public function headers(): array
    {
        return $this->headers;
    }
    
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function render(): void
    {
        $this->renderHeaders();

        echo $this->body;
    }

    protected function renderHeaders(): void
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
    }
}
