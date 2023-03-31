<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumValidationRule;
use PHPUnit\Framework\TestCase;

class MinimumValidationRuleTest extends TestCase
{
    public function testNotSetValidates(): void
    {
        $validatorRule = new MinimumValidationRule();
        $validated = $validatorRule->validate(null, "10");

        $this->assertEquals(true, $validated);
    }

    public function testNumberIsTheSameValidates(): void
    {
        $validatorRule = new MinimumValidationRule();
        $validated = $validatorRule->validate(10, "10");

        $this->assertEquals(true, $validated);
    }

    public function testNumberIsLessValidationFails(): void
    {
        $validatorRule = new MinimumValidationRule();
        $validated = $validatorRule->validate(5, "10");

        $this->assertEquals(false, $validated);
    }

    public function testNumberIsMoreValidates(): void
    {
        $validatorRule = new MinimumValidationRule();
        $validated = $validatorRule->validate(15, "10");

        $this->assertEquals(true, $validated);
    }
}
