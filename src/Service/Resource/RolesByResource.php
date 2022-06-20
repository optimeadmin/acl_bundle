<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource;

use Optime\Acl\Bundle\Entity\Resource;

/**
 * @author Manuel Aguirre
 */
class RolesByResource
{
    private ?string $parent;
    private array $children = [];

    public function __construct(
        private Resource $resource,
        private array $roles,
    ) {
        $this->parent = $this->resource->getParent();
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}