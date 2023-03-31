<?php

namespace Aolbrich\PhpRouter\Http\Request\Validator;

interface ValidationRuleInterface
{
    public function validate(mixed $value, string $validationParam = ''): bool;
    public function applyRule(mixed $value, string $validationParam = ''): bool;
    public function message(): string;
    public function isSet(mixed $value): bool;
}
