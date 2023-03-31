<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testReturnsUri(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);

        $uri = $request->getUri();

        $this->assertEquals('/', $uri);

        $_SERVER['REQUEST_URI'] = '/test';

        $uri = $request->getUri();

        $this->assertEquals('/test', $uri);
    }

    public function testReturnsMethod(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);

        $method = $request->getMethod();

        $this->assertEquals('GET', $method);

        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $method = $request->getMethod();

        $this->assertEquals('PATCH', $method);
    }

    public function testReturnsBody(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);
        $body = $request->body();

        $this->assertEquals('', $body);
    }

    public function testReturnsJsonBody(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);
        $body = $request->jsonBody();

        $this->assertNull($body);
    }

    public function testReturnAggregatedParams(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);
        $_GET = [
            'par1' => true,
        ];

        $_POST = [
            'par2' => true,
        ];
        $params = $request->params();

        $this->assertCount(2, $params);

        $this->assertEquals('par1', array_keys($params)[0]);
        $this->assertEquals('par2', array_keys($params)[1]);
    }

    /**
      * Note, the individual validation types are not tested here,
      * it is just testing the core functionality
      * they are tested at the validation rule level
     */
    public function testValidatatorWorks(): void
    {
        $container = new Container();
        $request = $container->get(Request::class);

        $_GET = [
            'par1' => 'param_value',
        ];

        $validated = $request->validate([
            'par1' => 'required'
        ]);

        // Assert validation succeeds
        $this->assertCount(1, $validated);
        $this->assertEquals('par1', array_keys($validated)[0]);
        $this->assertEquals('param_value', $validated['par1']);

        $validated = $request->validate([
            'par1' => 'required|max-length:2'
        ]);

        // Assert validation fails
        $this->assertCount(0, $validated);
        $errors = $request->validationErrors();
        $this->assertCount(1, $errors);
    }
}
