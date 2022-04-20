<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use ReflectionMethod;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceReferenceVoter extends Voter
{

    private array $previousResults = [];

    public function __construct(
        private RolesProviderInterface $rolesProvider,
        private ResourceRoleRepository $aclResourceRoleRepository,
        private ResourceReferenceRepository $aclResourceReferenceRepository,
        private bool $enabled
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if (!$this->enabled) {
            return true;
        }
        $reference = $subject;

        if (isset($this->previousResults[$token->getUserIdentifier()][$reference])) {
            dd('cache');
            return $this->previousResults[$token->getUserIdentifier()][$reference];
        }

        if (!$resourceReference = $this->aclResourceReferenceRepository->byName($reference)) {
            return false;
        }

        $currentRoles = $this->rolesProvider->getRolesByToken($token);

        $result = $this->aclResourceRoleRepository->verifyAccessToResourceByNameAndRoles(
            $resourceReference->getResource(),
            $currentRoles
        );

        return $this->previousResults[$token->getUserIdentifier()][$reference] ??= $result;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return 'resource_reference' === strtolower($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        return 'string' === $subjectType;
    }
}