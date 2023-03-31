<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function validate(mixed $value, string $validationParam = ''): bool
    {
        if (!$this->isset($value)) {
            return true;
        }

        return $this->applyRule($value, $validationParam);
    }

    public function applyRule($value, string $validationParam = ""): bool
    {
        return false;
    }

    public function message(): string
    {
        return '';
    }

    public function isSet(mixed $value): bool
    {
        return !($value === null || $value === '');
    }
}
