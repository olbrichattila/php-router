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
        ];
    }
}
