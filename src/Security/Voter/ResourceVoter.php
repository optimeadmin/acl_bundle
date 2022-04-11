<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\OptimeAclResourceRepository;
use Optime\Acl\Bundle\Repository\OptimeAclResourceRoleRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter
{

    public function __construct(
        private RolesProviderInterface          $rolesProvider,
        private OptimeAclResourceRepository     $aclResourceRepository,
        private OptimeAclResourceRoleRepository $aclResourceRoleRepository
    )
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return 'resource' === strtolower($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
//        if(!$token->getUser()){
//            return false;
//        }

        $currentRoles = $this->rolesProvider->getRolesByToken($token);

//        if (!count($currentRoles)) {
//            return false;
//        }

        if (!$aclResource = $this->aclResourceRepository->findOneBy(['name' => $subject])) {
            return false; //hay que definir que se hará en caso de no conseguir el registro
        }

        foreach ($currentRoles as $role) {
            if ($aclResourceRole = $this->aclResourceRoleRepository->findOneBy([
                'optimeAclResource' => $aclResource,
                'role' => $role
            ])) {
                return true; // si encuentra algun registro dará el acceso
            }
        }

        dd(
            $aclResource,
            $currentRoles,
            $subject
        );
        return true;
    }
}