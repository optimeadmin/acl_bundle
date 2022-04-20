<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
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

    public function verifyIfExistByResourceNameAndStringReference(string $resourceName, string $reference): bool
    {
        return is_null($this->getReferenceByResourceNameAndStringReference($resourceName, $reference));
    }

    public function referenceExists(string $name): bool
    {
        try {
            return (int)$this->createQueryBuilder('r')
                    ->select('COUNT(r)')
                    ->where('r.reference = :name')
                    ->setParameter('name', $name)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getSingleScalarResult() > 0;
        } catch (NoResultException) {
            return false;
        }
    }

    public function byName(string $name): ?ResourceReference
    {
        return $this->findOneBy(['reference' => $name]);
    }
}