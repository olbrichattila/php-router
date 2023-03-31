<?php

declare(strict_types=1);

namespace Aolbrich\Router\Test\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\RangeValidationRule;
use PHPUnit\Framework\TestCase;

class RangeValidationRuleTest extends TestCase
{
    public function testIfNotRequiredRetursTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate(null, '');

        $this->assertEquals(true, $validated);
    }

    public function testIfFallIntoRangeInAnyOrderReturnsTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('5', '6,2');

        $this->assertEquals(true, $validated);

        $validated = $validatorRule->validate('5', '2,6');

        $this->assertEquals(true, $validated);
    }

    public function testIfDoesNotFallIntoRangeInAnyOrderReturnsFalse(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('9', '6,2');

        $this->assertEquals(false, $validated);

        $validated = $validatorRule->validate('9', '2,6');

        $this->assertEquals(false, $validated);
    }

    public function testIfOnTopOfRangeInAnyOrderReturnsTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('6', '6,2');

        $this->assertEquals(true, $validated);

        $validated = $validatorRule->validate('6', '2,6');

        $this->assertEquals(true, $validated);
    }

    public function testIfOnBottomOfRangeInAnyOrderReturnsTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('2', '6,2');

        $this->assertEquals(true, $validated);

        $validated = $validatorRule->validate('2', '2,6');

        $this->assertEquals(true, $validated);
    }

    public function testIfRangeIsIdenticalAndFallIntoReturnsTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('4', '4,4');

        $this->assertEquals(true, $validated);
    }

    public function testIfRangeIsIdenticalAndDoesNotFallIntoReturnsTrue(): void
    {
        $validatorRule = new RangeValidationRule();
        $validated = $validatorRule->validate('5', '4,4');

        $this->assertEquals(false, $validated);
    }

    public function testIfRangeThrowsExceptionIfparameterIsInvalid(): void
    {
        $this->expectException(RequestValidationRuleException::class);

        $validatorRule = new RangeValidationRule();
        $validatorRule->validate('5', '4,4,9');
    }
}
