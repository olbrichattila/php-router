<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Rules;

use Aolbrich\PhpRouter\Http\Request\Validator\ValidationRuleInterface;

class RequiredValidationRule implements ValidationRuleInterface
{
    /**
     * Summary of validate
     * @param mixed $value
     * @return bool
     */
    public function validate(mixed $value): bool
    {
        return $value != null;
    }

    public function message(): string
    {
        return "required";
    }
}

// these requests may go to another library, and will be imported, injected