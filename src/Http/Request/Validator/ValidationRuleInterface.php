<?php

namespace Aolbrich\PhpRouter\Http\Request\Validator;

interface ValidationRuleInterface
{
    public function validate(mixed $value): bool;
    public function message(): string;
}