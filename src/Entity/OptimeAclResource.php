<?php

namespace Optime\Acl\Bundle\Entity;

use App\Entity\Application\ApplicationCatalog;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\OptimeAclResourceRepository;

#[ORM\Table("optime_acl_resource")]
#[ORM\Entity(repositoryClass: OptimeAclResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OptimeAclResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(
        name: 'created_at',
        updatable: false,
    )]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        nullable: true,
        insertable: false,
        updatable: false,
        columnDefinition: 'timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    )]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(
        mappedBy: 'optimeAclResource',
        targetEntity: OptimeAclResourceRole::class,
        cascade: ["persist"],
        orphanRemoval: true
    )]
    private Collection $optimeAclResourceRoles;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection
     */
    public function getOptimeAclResourceRoles(): Collection
    {
        return $this->optimeAclResourceRoles;
    }


}