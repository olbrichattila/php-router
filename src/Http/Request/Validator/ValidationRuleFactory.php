<?php

// @TODO add setter to be able to set custom config validation rule classes

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;
use Aolbrich\PhpRouter\Http\Request\Validator\Config\RequestValidatorConfig;
use Aolbrich\PhpRouter\Http\Request\Validator\Rules\Exception\RequestValidationRuleException;

class ValidationRuleFactory
{
    public function __construct(protected readonly RequestValidatorConfig $requestValidatorConfig)
    {
    }

    public function rule(string $ruleName): ValidationRuleInterface
    {
        $ruleClass = $this->getValidationRuleByName($ruleName);
        if ($ruleClass) {
            return new $ruleClass();
        }

        throw new RequestValidationRuleException('Validation rule ' . $ruleName . ' not exists!');
    }

    protected function getValidationRuleByName(string $ruleName): ?string
    {
        $config = $this->requestValidatorConfig->getConfig();

        return $config[$ruleName] ?? null;
    }
}
