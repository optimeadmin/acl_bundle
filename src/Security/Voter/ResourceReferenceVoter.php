<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceReferenceVoter extends Voter
{
    private array $previousResults = [];

    public function __construct(
        private RolesProviderInterface $rolesProvider,
        private ResourceRoleRepository $aclResourceRoleRepository,
        private ResourceReferenceRepository $aclResourceReferenceRepository,
        private bool $enabled,
        private bool $cacheResults,
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

        if ($this->cacheResults && isset($this->previousResults[$token->getUserIdentifier()][$reference])) {
            return $this->previousResults[$token->getUserIdentifier()][$reference];
        }

        if (!$resourceReference = $this->aclResourceReferenceRepository->byName($reference)) {
            return true;
        }

        if (!$resourceReference->getResource()->isActive()) {
            return true;
        }

        $result = $this->aclResourceRoleRepository->verifyAccessToResourceByNameAndRoles(
            $resourceReference->getResource(),
            $this->rolesProvider->getRolesByToken($token)
        );

        if (!$this->cacheResults) {
            return $result;
        }

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