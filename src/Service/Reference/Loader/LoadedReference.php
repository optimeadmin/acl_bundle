<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\Loader;

use Optime\Acl\Bundle\Entity\ResourceReference;
use function Symfony\Component\String\u;

/**
 * @author Manuel Aguirre
 */
class LoadedReference
{
    public const HIDDEN = '__HIDDEN__';

    private string $identifier;
    private string $modifiedResourceName;

    public function __construct(
        private string $name,
        private string $resource,
        private ?string $route,
        private ?string $routePath,
    ) {
        $this->setName($this->name);
        $this->modifiedResourceName = $this->resource;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getModifiedResourceName(): string
    {
        return $this->modifiedResourceName;
    }

    public function setModifiedResourceName(string $modifiedResourceName): void
    {
        $this->modifiedResourceName = $modifiedResourceName;
    }

    public function attachReference(ResourceReference $reference): void
    {
        $this->setName($reference->getReference());
        $this->resource = $this->modifiedResourceName = $reference->getResource()->getName();
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRoutePath(): ?string
    {
        return $this->routePath;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function isActive(): bool
    {
        return self::HIDDEN !== $this->getResource();
    }

    public function isEditedToActive(): bool
    {
        return self::HIDDEN !== $this->getModifiedResourceName();
    }

    private function setName(string $name): void
    {
        $this->name = $name;
        $this->identifier = u($this->getName())->snake()->toString();
    }
}