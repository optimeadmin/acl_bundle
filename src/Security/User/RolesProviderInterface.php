<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\User;

use Optime\Acl\Bundle\Entity\Resource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface RolesProviderInterface
{
    /**
     * Devuelve un arreglo con todos los roles/perfiles que maneja la aplicación.
     *
     * @return array|AclRole[]
     */
    public function getRoles(): array;

    /**
     * Devuelve un arreglo con los roles/perfiles del usuario en sesión.
     *
     * @param TokenInterface $token
     * @return array|AclRole[]
     */
    public function getRolesByToken(TokenInterface $token): array;

    /**
     * Devuelve un arreglo con los roles/perfiles asociados a un recurso.
     *
     * @param Resource $resource
     * @return array|AclRole[]
     */
    public function getRolesByResource(Resource $resource): array;
}