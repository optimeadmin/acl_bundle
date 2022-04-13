<?php

namespace Optime\Acl\Bundle\Security\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DefaultRolesProvider implements RolesProviderInterface
{
    public function __construct(private array $applicationRoles)
    {
    }

    public function getRoles(): array
    {
        return array_keys($this->applicationRoles);
    }

    public function getRolesByToken(TokenInterface $token): array
    {
        return ['ROLE_OPTIME_USER'];
        return $token->getRoleNames();
    }
}