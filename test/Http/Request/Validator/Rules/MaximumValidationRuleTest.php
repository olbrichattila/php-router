<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumValidationRule;
use PHPUnit\Framework\TestCase;

class MaximumValidationRuleTest extends TestCase
{
    public function testNotSetValidates(): void
    {
        $validatorRule = new MaximumValidationRule();
        $validated = $validatorRule->validate(null, "10");

        $this->assertEquals(true, $validated);
    }

    public function testNumberIsTheSameValidates(): void
    {
        $validatorRule = new MaximumValidationRule();
        $validated = $validatorRule->validate(10, "10");

        $this->assertEquals(true, $validated);
    }

    public function testNumberIsLessValidates(): void
    {
        $validatorRule = new MaximumValidationRule();
        $validated = $validatorRule->validate(5, "10");

        $this->assertEquals(true, $validated);
    }

    public function testNumberIsMoreValidationFails(): void
    {
        $validatorRule = new MaximumValidationRule();
        $validated = $validatorRule->validate(15, "10");

        $this->assertEquals(false, $validated);
    }
}
