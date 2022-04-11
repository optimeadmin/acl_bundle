<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\OptimeAclResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter
{

    public function __construct(
        private RolesProviderInterface $rolesProvider,
        private OptimeAclResourceRepository $aclResourceRepository
    )
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return 'resource' === strtolower($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        dump($this->aclResourceRepository->findAll(),$this->rolesProvider->getRolesByToken($token), $subject);
        return true;
    }
}