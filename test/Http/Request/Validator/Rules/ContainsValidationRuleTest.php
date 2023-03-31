<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\ContainsValidationRule;
use PHPUnit\Framework\TestCase;

class ContainsValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new ContainsValidationRule();
        $validated = $validatorRule->validate(null, '');

        $this->assertEquals(true, $validated);
    }

    public function testIfContainsReturnsTrue(): void
    {
        $validatorRule = new ContainsValidationRule();
        $validated = $validatorRule->validate('value2', 'value1,value2,value3');

        $this->assertEquals(true, $validated);
    }

    public function testIfNotContainsReturnsFalse(): void
    {
        $validatorRule = new ContainsValidationRule();
        $validated = $validatorRule->validate('value4', 'value1,value2,value3');

        $this->assertEquals(false, $validated);
    }
}
