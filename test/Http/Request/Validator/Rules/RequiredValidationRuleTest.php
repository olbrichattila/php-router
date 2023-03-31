<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\RequiredValidationRule;
use PHPUnit\Framework\TestCase;

class RequiredValidationRuleTest extends TestCase
{
    public function testRequiredFieldNotSet(): void
    {
        $validatorRule = new RequiredValidationRule();
        $validated = $validatorRule->validate(null, '');

        $this->assertEquals(false, $validated);
    }

    public function testRequiredFieldSet(): void
    {
        $validatorRule = new RequiredValidationRule();
        $validated = $validatorRule->validate("set", '');

        $this->assertEquals(true, $validated);
    }
}
