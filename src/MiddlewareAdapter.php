<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter;
use Closure;

class MiddlewareAdapter
{

    public function __construct(
        private readonly RouterService $routerService,
        private readonly array $beforeMiddleWares,
        private readonly array $afterMiddleWares,
    ) {

    }
    public function get(string $path, Closure|array $route): self
    {
        $this->routerService->get($path, $route, $this->beforeMiddleWares, $this->afterMiddleWares);

        return $this;
    }
}