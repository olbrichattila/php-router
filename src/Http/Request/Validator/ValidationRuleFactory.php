<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator;
use Aolbrich\PhpRouter\Http\Request\Validator\Config\RequestValidatorConfig;

class ValidationRuleFactory
{
    public function __construct(protected readonly RequestValidatorConfig $requestValidatorConfig) {}

    public function rule(string $ruleName): ValidationRuleInterface
    {
        $ruleClass = $this->getValidationRuleName($ruleName);
        // @todo check instance of
        if ($ruleClass) {
            return new $ruleClass();
        }
        
        throw new \Exception('Validation rule ' . $ruleName . ' not exists!');
    }

    protected function getValidationRuleName(string $ruleName): ?string
    {
        $config = $this->requestValidatorConfig->getConfig();

        return $config[$ruleName] ?? null;
    }
}
