<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\FloatValidationRule;
use PHPUnit\Framework\TestCase;

class FloatValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new FloatValidationRule();
        $validated = $validatorRule->validate(null);

        $this->assertEquals(true, $validated);
    }

    public function testIfIngtegerReturnsTrue(): void
    {
        $validatorRule = new FloatValidationRule();
        $validated = $validatorRule->validate('123');

        $this->assertEquals(true, $validated);
    }

    public function testIfFloatReturnsTrue(): void
    {
        $validatorRule = new FloatValidationRule();
        $validated = $validatorRule->validate('123.12');

        $this->assertEquals(true, $validated);
    }

    public function testIfNotFloatOrIntegerReturnsFalse(): void
    {
        $validatorRule = new FloatValidationRule();
        $validated = $validatorRule->validate('text');

        $this->assertEquals(false, $validated);
    }
}
