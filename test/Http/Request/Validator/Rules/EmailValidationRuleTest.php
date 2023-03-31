<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\EmailValidationRule;
use PHPUnit\Framework\TestCase;

class EmailValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new EmailValidationRule();
        $validated = $validatorRule->validate(null, '');

        $this->assertEquals(true, $validated);
    }

    public function testIfEmailFormatIsValidRetursTrue(): void
    {
        $validatorRule = new EmailValidationRule();
        $validated = $validatorRule->validate('testemail@test.com');

        $this->assertEquals(true, $validated);
    }

    public function testIfEmailFormatIsNotValidRetursFalse(): void
    {
        $validatorRule = new EmailValidationRule();
        $validated = $validatorRule->validate('testemailinvalidformat');

        $this->assertEquals(false, $validated);
    }
}
