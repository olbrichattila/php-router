<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\RegexValidationRule;
use PHPUnit\Framework\TestCase;

class RegexValidationRuleTest extends TestCase
{
    public function testNotSetValidates(): void
    {
        $validatorRule = new RegexValidationRule();
        $validated = $validatorRule->validate(null, "10");

        $this->assertEquals(true, $validated);
    }

    public function testValidatesIfRegexMatches(): void
    {
        $validatorRule = new RegexValidationRule();
        $validated = $validatorRule->validate('1234', '/^[0-9]+$/');

        $this->assertEquals(true, $validated);
    }

    public function testValidationFailsIfRegexDoesNotMatch(): void
    {
        $validatorRule = new RegexValidationRule();
        $validated = $validatorRule->validate('12ab34', '/^[0-9]+$/');

        $this->assertEquals(false, $validated);
    }

    public function testExceptionThrownIfRegexInvalid(): void
    {
        $this->expectException(RequestValidationRuleException::class);

        $validatorRule = new RegexValidationRule();
        $validatorRule->validate('1234', '/(\r/');
    }
}
