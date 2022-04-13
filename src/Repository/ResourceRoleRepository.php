<?php

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Entity\ResourceRole;

class ResourceRoleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceRole::class);
    }

    public function verifyAccessToResourceByNameAndRoles(Resource $aclResource, array $roles): bool
    {
        $result = $this->createQueryBuilder('oaclrr')
            ->innerJoin('oaclrr.optimeAclResource', 'oaclr')
            ->where('oaclrr.role IN (:roles)')
            ->andWhere('oaclr = :optimeAclResource OR oaclr.name LIKE :optimeAclResourceLike')
            ->setParameters([
                'optimeAclResource' => $aclResource,
                'optimeAclResourceLike' => $aclResource->getName() . ' %',
                'roles' => $roles
            ])
            ->getQuery()
            ->getResult();

        return 0 < count($result);
    }
}