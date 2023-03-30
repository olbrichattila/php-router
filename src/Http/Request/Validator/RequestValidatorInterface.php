<?php

namespace Aolbrich\PhpRouter\Http\Request\Validator;

use Aolbrich\PhpRouter\Http\Request\RequestInterface;

interface RequestValidatorInterface
{
    public function validate(RequestInterface $request, array $validationRules): RequestValidatorInterface;
    public function validated(): array;
    public function validationErrors(): array;
}