<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class EmailValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;
        
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function message(): string
    {
        return "Email format is incorrect '{$this->validationParam}'";
    }
}
