<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class ContainsValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;
        
        $contains = array_map('trim', explode(',', $validationParam));

        return in_array($value, $contains);
    }

    public function message(): string
    {
        return "Value does not contain any of the following '{$this->validationParam}'";
    }
}
