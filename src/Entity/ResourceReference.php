<?php

namespace Optime\Acl\Bundle\Entity;

use App\Entity\Application\Application;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;

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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Resource
     */
    public function getOptimeAclResource(): Resource
    {
        return $this->optimeAclResource;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->reference;
    }


}