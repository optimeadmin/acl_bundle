<?php

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Optime\Acl\Bundle\Entity\ResourceReference;

class ResourceReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceReference::class);
    }

    public function getReferenceByResourceNameAndStringReference(
        string $resourceName,
        string $reference
    ): ?ResourceReference {
        return $this->createQueryBuilder('resourceReference')
            ->innerJoin('resourceReference.resource', 'resource')
            ->where('resourceReference.reference = :reference')
            ->andWhere('resource.name = :resource')
            ->setParameters([
                'resource' => $resourceName,
                'reference' => $reference
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function hasByResourceAndReference(string $resourceName, string $reference): bool
    {
        return is_null($this->getReferenceByResourceNameAndStringReference($resourceName, $reference));
    }

    public function findOneByReference(string $reference): ?ResourceReference
    {
        return $this->findOneBy(['reference' => $reference]);
    }

    public function hasByReference(string $reference): bool
    {
        return !is_null($this->findOneByReference($reference));
    }
}