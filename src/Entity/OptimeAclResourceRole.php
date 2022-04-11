<?php

namespace Optime\Acl\Bundle\Entity;

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
}