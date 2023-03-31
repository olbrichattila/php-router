<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;
use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class RangeValidationRule extends ValidationRuleBase implements ValidationRuleInterface
{
    private string $validationParam;

    public function applyRule(mixed $value, string $validationParam = ''): bool
    {
        $this->validationParam = $validationParam;
        
        $ranges = array_map('trim', explode(',', $validationParam));
        if (count($ranges) !== 2) {
            throw new RequestValidationRuleException("Incorrect range provided {$validationParam}");
        }
        sort($ranges);

        return $value >= $ranges[0] && $value <= $ranges[1];
    }

    public function message(): string
    {
        return "The value does not fall into range '{$this->validationParam}'";
    }
}
