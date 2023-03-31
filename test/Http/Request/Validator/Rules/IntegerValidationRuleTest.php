<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\IntegerValidationRule;
use PHPUnit\Framework\TestCase;

class IntegerValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new IntegerValidationRule();
        $validated = $validatorRule->validate(null);

        $this->assertEquals(true, $validated);
    }

    public function testIfIntegerReturnsTrue(): void
    {
        $validatorRule = new IntegerValidationRule();
        $validated = $validatorRule->validate('123');

        $this->assertEquals(true, $validated);
    }

    public function testIfNotIngtegerReturnsFalse(): void
    {
        $validatorRule = new IntegerValidationRule();
        $validated = $validatorRule->validate('text');

        $this->assertEquals(false, $validated);
    }
}
