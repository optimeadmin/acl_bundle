<?php

namespace Optime\Acl\Bundle\Security\User;

use Optime\Acl\Bundle\Entity\Resource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use function array_diff;
use function array_keys;
use function array_map;

class DefaultRolesProvider implements RolesProviderInterface
{
    public function __construct(
        private RoleHierarchyInterface $roleHierarchy,
        private array $applicationRoles
    ) {
    }

    /**
     * @return array|AclRole[]
     */
    public function getRoles(): array
    {
        return $this->mapToAclRoles(
            $this->roleHierarchy->getReachableRoleNames(array_keys($this->applicationRoles))
        );
    }

    /**
     * @param TokenInterface $token
     * @return array|AclRole[]
     */
    public function getRolesByToken(TokenInterface $token): array
    {
        return $this->mapToAclRoles($token->getRoleNames());
    }

    /**
     * @param Resource $resource
     * @return array|AclRole[]
     */
    public function getRolesByResource(Resource $resource): array
    {
        return $this->mapToAclRoles($resource->getRoles()->toArray());
    }

    /**
     * @param array|string[] $securityRoles
     * @return array|AclRole[]
     */
    protected function mapToAclRoles(array $securityRoles): array
    {
        $securityRoles = array_diff($securityRoles, ['ROLE_ALLOWED_TO_SWITCH']);

        return array_map(fn($role) => AclRole::fromSecurityRole($role, $this->roleHierarchy), $securityRoles);
    }
}