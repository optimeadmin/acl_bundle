<?php

namespace Optime\Acl\Bundle\Security\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface RolesProviderInterface
{
    public function getRoles(): array;

    public function getRolesByToken(TokenInterface $token): array;
}