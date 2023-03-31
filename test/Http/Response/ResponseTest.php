<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Response;

use Aolbrich\PhpRouter\Http\Response\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testResponseCodeSetters(): void
    {
        $response = new Response();

        $this->assertEquals(200, $response->getResponseCode());

        $response->setResponseCode(404);

        $this->assertEquals(404, $response->getResponseCode());
    }

    public function testResponseBodySetters(): void
    {
        $response = new Response();

        $this->assertEquals('', $response->getBody());

        $response->setBody('it works');

        $this->assertEquals('it works', $response->getBody());
    }

    public function testHeaders(): void
    {
        $response = new Response();

        $this->assertCount(0, $response->headers());

        $response->setHeader('key', 'value');

        $this->assertCount(1, $response->headers());
    }
}
