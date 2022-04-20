<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;
use function preg_replace;
use function str_contains;
use function substr_count;

#[ORM\Table("optime_acl_resource")]
#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(unique: true)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column]
    private bool $visible;

    #[ORM\Column]
    private bool $createdByUser;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(
        nullable: true,
        insertable: false,
        updatable: false,
        columnDefinition: 'timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    )]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(
        mappedBy: 'resource',
        targetEntity: ResourceReference::class,
        cascade: ['all'],
        orphanRemoval: true
    )]
    private Collection $references;

    #[ORM\OneToMany(
        mappedBy: 'resource',
        targetEntity: ResourceRole::class,
        cascade: ['all'],
        orphanRemoval: true
    )]
    private Collection $roles;

    public function __construct(string $name, string $reference = null, bool $createdByUser = false)
    {
        $this->name = $name;
        $this->createdByUser = $createdByUser;
        $this->visible = LoadedReference::HIDDEN !== $name;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->references = new ArrayCollection();
        $this->roles = new ArrayCollection();

        if (null !== $reference) {
            $this->addReference($reference);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function hasParent(): bool
    {
        return str_contains(trim($this->getName()), ' ');
    }

    public function getParent(): ?string
    {
        if (!$this->hasParent()) {
            return null;
        }

        return trim(preg_replace('/(^.+)(\s[^\s]+)$/', '$1', $this->getName()));
    }

    public function getLevel(): int
    {
        if (!$this->hasParent()) {
            return 0;
        }

        return substr_count(trim($this->getName()), ' ');
    }

    public function isCreatedByUser(): bool
    {
        return $this->createdByUser;
    }

    public function getReferences(): Collection
    {
        return $this->references->map(fn(ResourceReference $resourceReference) => $resourceReference->getReference());
    }

    public function addReference(string $name): void
    {
        if (!$this->getReferenceByName($name)) {
            $this->references->add(new ResourceReference($this, $name));
        }
    }

    public function removeReference(string $name): void
    {
        if ($reference = $this->getReferenceByName($name)) {
            $this->references->removeElement($reference);
        }
    }

    public function getRoles(): ArrayCollection|Collection
    {
        return $this->roles->map(fn(ResourceRole $role) => $role->getRole());
    }

    public function addRole(string|int $role): void
    {
        if (!$this->getRoleByValue($role)) {
            $this->roles->add(new ResourceRole($this, (string)$role));
        }
    }

    public function removeRole(string|int $role): void
    {
        if ($relation = $this->getRoleByValue($role)) {
            $this->roles->removeElement($relation);
        }
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    private function getReferenceByName(string $name): ?ResourceReference
    {
        foreach ($this->references as $reference) {
            if ($reference->getReference() === $name) {
                return $reference;
            }
        }

        return null;
    }

    private function getRoleByValue(string $role): ?ResourceRole
    {
        foreach ($this->roles as $resourceRole) {
            if ($resourceRole->getRole() === $role) {
                return $resourceRole;
            }
        }

        return null;
    }
}