<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Config;

use Aolbrich\PhpRouter\Http\Request\Validator\Config\RequestValidatorConfig;
use PHPUnit\Framework\TestCase;

class RequestValidatorConfigTest extends TestCase
{
    public function testConfigReturnsArray(): void
    {
        $config = new RequestValidatorConfig();
        $result = $config->getConfig();

        $this->assertIsArray($result);
    }
}
