<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class MinimumValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam = '';

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;

        return $value >= $validationParam;
    }

    public function message(): string
    {
        return "Value should be more or equal then {$this->validationParam}";
    }
}
