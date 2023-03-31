<?php

declare(strict_types=1);

namespace Aolbrich\PhpRouter\Http\Request\Validator\Config;

use Aolbrich\PhpRouter\Http\Request\Validator\Config\RequestValidatorConfigInterface;

class RequestValidatorConfig implements RequestValidatorConfigInterface
{
    public function getConfig(): array
    {
        return [
            'required' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\RequiredValidationRule::class,
            'min' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumValidationRule::class,
            'max' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumValidationRule::class,
            'min-length' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MinimumLengthValidationRule::class,
            'max-length' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\MaximumLengthValidationRule::class,
            'regex' => \Aolbrich\PhpRouter\Http\Request\Validator\Rules\RegexValidationRule::class,
            // @todo add contains|1,5,3
            // @todo add range|1,5
            // @todo add date|yyyy-mm-dd
            // @todo add confirm-password|othepasswordfieldname
            // @todo add possiblility to pass closure and create unique rule
            // @todo add setter to extend rules with custom rules (may have to go to request class as well)
        ];
    }
}
