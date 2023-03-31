<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class DateValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;

        return (bool) strtotime($value);
    }

    public function message(): string
    {
        return "Date format is incorrect, shoud be '{$this->validationParam}'";
    }
}
