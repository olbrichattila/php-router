<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\DateValidationRule;
use PHPUnit\Framework\TestCase;

class DateValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new DateValidationRule();
        $validated = $validatorRule->validate(null, '');

        $this->assertEquals(true, $validated);
    }

    public function testIfDateFormatIsInCorrect(): void
    {
        $validatorRule = new DateValidationRule();
        $validated = $validatorRule->validate('2023-13-01', '');

        $this->assertEquals(false, $validated);
    }

    public function testIfDateFormatIsCorrect(): void
    {
        $validatorRule = new DateValidationRule();
        $validated = $validatorRule->validate('2023-02-12', '');

        $this->assertEquals(true, $validated);
    }
}
