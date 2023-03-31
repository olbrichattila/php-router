<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Config;

use Aolbrich\PhpRouter\Http\Request\Validator\Config\RequestValidatorConfigInterface;
use Closure;

class RequestValidatorConfig implements RequestValidatorConfigInterface
{
    protected const FIXED_RULES = [
        'required' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\RequiredValidationRule::class,
        'min' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumValidationRule::class,
        'max' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumValidationRule::class,
        'min-length' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumLengthValidationRule::class,
        'max-length' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumLengthValidationRule::class,
        'regex' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\RegexValidationRule::class,
        'contains' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\ContainsRequiredRule::class,
        'range' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\RangeValidationRule::class,
        'email' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\EmailValidationRule::class,
        'date' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\DateValidationRule::class,
        'integer' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\IntegerValidationRule::class,
        'int' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\IntegerValidationRule::class,
        'float' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\FloatValidationRule::class,
        // @todo add confirm-password|othepasswordfieldname (this might be trickier)
    ];

    protected array $customRules = [];

    public function setRule(string $ruleName, string|callable|Closure $rule): void
    {
        $this->customRules[$ruleName] = $rule;
    }

    public function getConfig(): array
    {
        return array_merge(self::FIXED_RULES, $this->customRules);
    }
}
