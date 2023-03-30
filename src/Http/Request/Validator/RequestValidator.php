<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator;

use Aolbrich\PhpRouter\Http\Request\RequestInterface;

class RequestValidator implements RequestValidatorInterface
{
    public function __construct(protected readonly ValidationRuleFactory $validationRuleFactory)
    {}
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
        foreach($validationRules as $field => $validationRuleName) {
            $value = $params[$field] ?? null;
            $validationRule = $this->validationRuleFactory->rule($validationRuleName);
            $validationResult = $validationRule->validate($value);

            if ($validationResult === true) {
                $this->validated[$field] = $value;
                continue;
            }

            $this->validationErrors[$field] = $validationRule->message();
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
}
