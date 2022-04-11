<?php

namespace Optime\Acl\Bundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Optime\Acl\Bundle\Entity\OptimeAclResourceRole;

class OptimeAclResourceRoleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OptimeAclResourceRole::class);
    }
}