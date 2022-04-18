<?php

namespace Optime\Acl\Bundle\Security\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use function array_keys;

class DefaultRolesProvider implements RolesProviderInterface
{
    public function __construct(
        private RoleHierarchyInterface $roleHierarchy,
        private array $applicationRoles
    ) {
    }

    public function getRoles(): array
    {
        return $this->roleHierarchy->getReachableRoleNames(array_keys($this->applicationRoles));
    }

    public function getRolesByToken(TokenInterface $token): array
    {
        return $token->getRoleNames();
    }
}