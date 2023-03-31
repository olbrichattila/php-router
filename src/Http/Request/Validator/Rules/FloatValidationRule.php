<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class FloatValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;
        
        // note is_float does not work in this case as string
        return (bool) filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    public function message(): string
    {
        return "Value is not a floating point number '{$this->validationParam}'";
    }
}
