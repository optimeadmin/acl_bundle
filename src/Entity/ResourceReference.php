<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;

#[ORM\Table("optime_acl_resource_reference")]
#[ORM\Entity(repositoryClass: ResourceReferenceRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
#[ORM\Index(fields: ["reference"], name: "IDX_OPTIME_ACL_REFERENCE_NAME")]
class ResourceReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(
        inversedBy: 'references'
    )]
    private Resource $resource;

    #[ORM\Column]
    private string $reference;

    #[ORM\Column(
        name: 'created_at',
        updatable: false,
    )]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(
        nullable: true,
        insertable: false,
        updatable: false,
        columnDefinition: "timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)'",
        options: ['default' => 'CURRENT_TIMESTAMP'],
    )]
    private DateTimeImmutable $updatedAt;

    public function __construct(Resource $resource, string $reference)
    {
        $this->resource = $resource;
        $this->reference = $reference;
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

    public function getReference(): string
    {
        return $this->reference;
    }
}