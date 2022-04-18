<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;

#[ORM\Table("optime_acl_resource_role")]
#[ORM\Entity(repositoryClass: ResourceRoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class ResourceRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn]
    private Resource $resource;

    #[ORM\Column(length: 255, nullable: false)]
    private string $role;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(
        nullable: true,
        insertable: false,
        updatable: false,
        columnDefinition: 'timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    )]
    private DateTimeImmutable $updatedAt;

    public function __construct(Resource $resource, string $role)
    {
        $this->resource = $resource;
        $this->role = $role;

        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function getRole(): string
    {
        return $this->role;
    }


}