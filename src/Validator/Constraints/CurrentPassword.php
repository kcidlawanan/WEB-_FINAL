<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CurrentPassword extends Constraint
{
    public string $message = 'Current password is incorrect.';

    public function validatedBy(): string
    {
        return CurrentPasswordValidator::class;
    }
}
