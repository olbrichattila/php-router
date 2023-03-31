<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumLengthValidationRule;
use PHPUnit\Framework\TestCase;

class MaximumLengthValidationRuleTest extends TestCase
{
    public function testNotSetValidates(): void
    {
        $validatorRule = new MaximumLengthValidationRule();
        $validated = $validatorRule->validate(null, "3");

        $this->assertEquals(true, $validated);
    }

    public function testLengthIsTheSameValidates(): void
    {
        $validatorRule = new MaximumLengthValidationRule();
        $validated = $validatorRule->validate("abc", "3");

        $this->assertEquals(true, $validated);
    }

    public function testLengthIsLessValidates(): void
    {
        $validatorRule = new MaximumLengthValidationRule();
        $validated = $validatorRule->validate("ab", "3");

        $this->assertEquals(true, $validated);
    }

    public function testLengthIsMoreValidationFails(): void
    {
        $validatorRule = new MaximumLengthValidationRule();
        $validated = $validatorRule->validate("abcd", "3");

        $this->assertEquals(false, $validated);
    }
}
