<?php

namespace Optime\Acl\Bundle\Security\User;

use Optime\Acl\Bundle\Entity\Resource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface RolesProviderInterface
{
    /**
     * @return array|AclRole[]
     */
    public function getRoles(): array;

    /**
     * @param TokenInterface $token
     * @return array|AclRole[]
     */
    public function getRolesByToken(TokenInterface $token): array;

    /**
     * @param Resource $resource
     * @return array|AclRole[]
     */
    public function getRolesByResource(Resource $resource): array;
}