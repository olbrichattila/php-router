<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\MockMidlewares;

class MockMiddleware {
    
    public int $callCount = 0;

    public function handle(): void {
        $this->callCount++;
    }
}


