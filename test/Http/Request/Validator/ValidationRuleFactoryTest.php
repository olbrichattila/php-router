<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator;

use Aolbrich\PhpDiContainer\Container;
use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleFactory;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\RequiredValidationRule;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use PHPUnit\Framework\TestCase;

class ValidationRuleFactoryTest extends TestCase
{
    public function testIfCorrectRuleReturned(): void
    {
        $container = new Container();
        $validationRuleFactory = $container->get(ValidationRuleFactory::class);

        $rule = $validationRuleFactory->rule('required');

        $this->assertInstanceOf(RequiredValidationRule::class, $rule);
    }

    public function testIfExceptionThrownWithInvalidRule(): void
    {
        $this->expectException(RequestValidationRuleException::class);

        $container = new Container();
        $validationRuleFactory = $container->get(ValidationRuleFactory::class);
        $validationRuleFactory->rule('invalidrule');
    }
}
