<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Security\User;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use function array_diff;

/**
 * @author Manuel Aguirre
 */
class AclRole
{
    public function __construct(
        private string|int $role,
        private string $label,
        private array $parentRoles = [],
    ) {
    }

    public static function fromSecurityRole(string $role, ?RoleHierarchyInterface $roleHierarchy = null): self
    {
        if ($roleHierarchy instanceof RoleHierarchyInterface) {
            $parentRoles = array_diff($roleHierarchy->getReachableRoleNames([$role]), [$role]);
        } else {
            $parentRoles = [];
        }

        return new self($role, $role, $parentRoles);
    }

    public function getRole(): int|string
    {
        return $this->role;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getParentRoles(): array
    {
        return $this->parentRoles;
    }

    public function __toString(): string
    {
        return $this->getRole();
    }
}