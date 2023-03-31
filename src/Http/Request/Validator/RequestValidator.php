<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator;

use Aolbrich\PhpRouter\Http\Request\RequestInterface;

class RequestValidator implements RequestValidatorInterface
{
    public function __construct(protected readonly ValidationRuleFactory $validationRuleFactory)
    {
    }
    protected array $validationRules = [];
    protected array $validated = [];
    protected array $validationErrors = [];

    public function validate(
        RequestInterface $request,
        array $validationRules
    ): RequestValidatorInterface {
        $this->validated = [];
        $this->validationErrors = [];
        $params = $request->params();

        foreach ($validationRules as $field => $validationRuleName) {
            $value = $params[$field] ?? null;
            $this->applyValidations($params, $value, $field, $validationRuleName);
        }

        return $this;
    }
    public function validated(): array
    {
        return $this->validated;
    }
    public function validationErrors(): array
    {
        return $this->validationErrors;
    }

    protected function applyValidations(
        array $params,
        mixed $value,
        string $field,
        string $validationRuleName
    ): void {
        $validated = true;
        foreach (explode('|', $validationRuleName) as $splittedRule) {
            if ($this->applyValidation($params, $value, $field, $splittedRule) === false) {
                $validated = false;
            };
        }

        if ($validated === true) {
            $this->validated[$field] = $value;
        }
    }

    protected function applyValidation(
        array $params,
        mixed $value,
        string $field,
        string $validationRuleName
    ): bool {
        $ruleParams = explode(':', $validationRuleName);
        $filteredValidationRuleName = $ruleParams[0];
        $ruleParam = $ruleParams[1] ?? '';
        $validationRule = $this->validationRuleFactory->rule($filteredValidationRuleName);
        $validationResult = $validationRule->validate($value, $ruleParam);

        if ($validationResult === true) {
            return true;
        }

        $this->validationErrors[$field] = $validationRule->message();

        return false;
    }
}
