<?php

namespace Optime\Acl\Bundle\Entity;

use App\Entity\Application\Application;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\OptimeAclResourceRoleRepository;

#[ORM\Table("optime_acl_resource_role")]
#[ORM\Entity(repositoryClass: OptimeAclResourceRoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OptimeAclResourceRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'optimeAclResourceRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private OptimeAclResource $optimeAclResource;

    #[ORM\Column(length: 255, nullable: false)]
    private string $role;

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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return OptimeAclResource
     */
    public function getOptimeAclResource(): OptimeAclResource
    {
        return $this->optimeAclResource;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }


}