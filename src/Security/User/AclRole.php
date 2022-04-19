<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Security\User;

/**
 * @author Manuel Aguirre
 */
class AclRole
{
    public function __construct(
        private string|int $role,
        private string $label
    ) {
    }

    public static function fromSecurityRole(string $role): self
    {
        return new self($role, $role);
    }

    public function getRole(): int|string
    {
        return $this->role;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function __toString(): string
    {
        return $this->getRole();
    }
}