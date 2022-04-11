<?php

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
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
}