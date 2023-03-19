<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\MockControllers;
use Aolbrich\PhpRouter\Request\MockRequest;

class MockController {
    
    public int $callCount = 0;

    public ?int $par1 = null;
    public ?string $par2 = null;
    public function index(): void
    {
        $this->callCount++;
    }

    public function paramteterTest(string $par2, MockRequest $request, int $par1): void
    {
        $this->par1 = $par1;
        $this->par2 = $par2;
    }
}
