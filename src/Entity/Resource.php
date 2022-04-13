<?php

namespace Optime\Acl\Bundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Optime\Acl\Bundle\Repository\ResourceRepository;

#[ORM\Table("optime_acl_resource")]
#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private bool $visible;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(
        nullable: true,
        insertable: false,
        updatable: false,
        columnDefinition: 'timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    )]
    private DateTimeImmutable $updatedAt;

    public function __construct(string $name, bool $visible)
    {
        $this->name = $name;
        $this->visible = $visible;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

}