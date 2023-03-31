<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class RegexValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;

        $match = @preg_match($validationParam, (string) $value);
        if ($match === false) {
            throw new RequestValidationRuleException("Validation rule error: invalid regex '{$validationParam}'");
        }

        return (bool) $match;
    }

    public function message(): string
    {
        return "Value does not match reges {$this->validationParam}";
    }
}
