<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumLengthValidationRule;
use PHPUnit\Framework\TestCase;

class MinimumLengthValidationRuleTest extends TestCase
{
    public function testNotSetValidates(): void
    {
        $validatorRule = new MinimumLengthValidationRule();
        $validated = $validatorRule->validate(null, "3");

        $this->assertEquals(true, $validated);
    }

    public function testLengthIsTheSameValidates(): void
    {
        $validatorRule = new MinimumLengthValidationRule();
        $validated = $validatorRule->validate("abc", "3");

        $this->assertEquals(true, $validated);
    }

    public function testLengthIsLessValidationFails(): void
    {
        $validatorRule = new MinimumLengthValidationRule();
        $validated = $validatorRule->validate("ab", "3");

        $this->assertEquals(false, $validated);
    }

    public function testLengthIsMoreValidates(): void
    {
        $validatorRule = new MinimumLengthValidationRule();
        $validated = $validatorRule->validate("abcd", "3");

        $this->assertEquals(true, $validated);
    }
}
