<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Optime\Acl\Bundle\Entity\Resource;
use function is_string;

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

    /**
     * @return array|Resource[]
     */
    public function allVisible(): array
    {
        return $this->findBy(['visible' => true], [
            'name' => 'ASC',
        ]);
    }

    /**
     * @return array|Resource[]
     */
    public function getUnused(): array
    {
        return $this->createQueryBuilder('resource')
            ->leftJoin('resource.references', 'references')
            ->leftJoin('resource.roles', 'roles')
            ->where('resource.createdByUser = false')
            ->groupBy('resource.id')
            ->having('COUNT(roles) = 0')
            ->andHaving('COUNT(references) = 0')
            ->orderBy('resource.name', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function hasChildren(string|Resource $resource): bool
    {
        $name = is_string($resource) ? $resource : $resource->getName();

        try {
            return (int)$this->createQueryBuilder('resource')
                    ->select('COUNT(resource)')
                    ->where('resource.name LIKE :parent_name')
                    ->setParameter('parent_name', $name . ' %')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getSingleScalarResult() > 0;
        } catch (NoResultException $e) {
            return false;
        }
    }
}