<?php

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;

#[ORM\Table("optime_acl_resource_reference")]
#[ORM\Entity(repositoryClass: ResourceReferenceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ResourceReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn]
    private Resource $optimeAclResource;

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
        columnDefinition: 'timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    )]
    private DateTimeImmutable $updatedAt;

    public function __construct(Resource $resource, string $reference)
    {
        $this->optimeAclResource = $resource;
        $this->reference = $reference;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOptimeAclResource(): Resource
    {
        return $this->optimeAclResource;
    }

    public function getRole(): string
    {
        return $this->reference;
    }


}