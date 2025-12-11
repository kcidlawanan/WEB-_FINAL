<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentPasswordValidator extends ConstraintValidator
{
    private UserPasswordHasherInterface $passwordHasher;
    private TokenStorageInterface $tokenStorage;

    public function __construct(UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage)
    {
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user) {
            return;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
