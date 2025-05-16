<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

readonly class SecurityHelper
{
    public function __construct(private Security $security)
    {
    }

    public function getUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('You must be authenticated.');
        }

        return $user;
    }

    public function hasRole(string $expectedRole): bool
    {
        $user = $this->getUser();
        return $user->getRole()?->value === $expectedRole;
    }
}
