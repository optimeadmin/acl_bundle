<?php

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Optime\Acl\Bundle\Entity\Resource;

class ResourceRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    public function findOneByName(string $name): ?Resource
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function hasByName(string $name): bool
    {
        return !is_null($this->findOneByName($name));
    }
}