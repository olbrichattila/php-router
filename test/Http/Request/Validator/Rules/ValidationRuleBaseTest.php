<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\ValidationRuleBase;
use PHPUnit\Framework\TestCase;

class ValidationRuleBaseTest extends TestCase
{
    public function testIfValueNotSetAlwaysShouldValidate(): void
    {
        $validatorRule = new ValidationRuleBase();
        $validated = $validatorRule->validate(null, "");

        $this->assertEquals(true, $validated);
    }

    public function testValueSetShouldNotValidate(): void
    {
        $validatorRule = new ValidationRuleBase();
        $validated = $validatorRule->validate('1234', '');

        $this->assertEquals(false, $validated);
    }
}
