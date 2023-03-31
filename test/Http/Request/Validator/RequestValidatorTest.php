<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Validator\RequestValidator;
use Aolbrich\PhpRouter\Http\Request\MockRequest;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    public function testIfValidatorSuccceds(): void
    {
        $container = new Container();
        $requestValidator = $container->get(RequestValidator::class);
        $request = new MockRequest();
        $request->setParams(
            [
                'par1' => 20,
            ]
        );

        $rules = [
            'par1' => 'required|min:10|max:20',
        ];

        $requestValidator->validate($request, $rules);
        $validated = $requestValidator->validated();
        $validationErrors = $requestValidator->validationErrors();

        $this->assertCount(1, $validated);
        $this->assertCount(0, $validationErrors);
    }

    public function testIfValidatorFails(): void
    {
        $container = new Container();
        $requestValidator = $container->get(RequestValidator::class);
        $request = new MockRequest();
        $request->setParams(
            [
                'par1' => 20,
                'par2' => 10,
            ]
        );

        $rules = [
            'par1' => 'required|min:30',
            'par2' => 'required|max:5',
        ];

        $requestValidator->validate($request, $rules);
        $validated = $requestValidator->validated();
        $validationErrors = $requestValidator->validationErrors();

        $this->assertCount(0, $validated);
        $this->assertCount(2, $validationErrors);
    }

    public function testIfErrorThrownIfInvalidValidationRudeGiven(): void
    {
        $this->expectException(RequestValidationRuleException::class);

        $container = new Container();
        $requestValidator = $container->get(RequestValidator::class);
        $request = new MockRequest();

        $rules = [
            'par1' => 'invalidrule',
        ];

        $requestValidator->validate($request, $rules);
    }
}
