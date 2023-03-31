<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class IntegerValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;
        
        // note is_int does not work in this case as string
        return (bool) filter_var($value, FILTER_VALIDATE_INT);
    }

    public function message(): string
    {
        return "Value is not integer '{$this->validationParam}'";
    }
}
