<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceRouteVoter extends Voter
{

    private array $previousResults = [];

    public function __construct(
        private RolesProviderInterface $rolesProvider,
        private ResourceRoleRepository $aclResourceRoleRepository,
        private ResourceReferenceRepository $aclResourceReferenceRepository,
        private RouterInterface $router,
        private bool $enabled
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $routeName = $subject;

        if (!$this->enabled) {
            return true;
        }

        if (isset($this->previousResults[$token->getUserIdentifier()][$routeName])) {
            return $this->previousResults[$token->getUserIdentifier()][$routeName];
        }

        if (!$route = $this->router->getRouteCollection()->get($routeName)) {
            return true;
        }

        if (!$resourceReference = $this->aclResourceReferenceRepository->byName($route->getDefault('_controller'))) {
            return true;
        }

        if (!$resourceReference->getResource()->isActive()) {
            return true;
        }

        $currentRoles = $this->rolesProvider->getRolesByToken($token);

        $result = $this->aclResourceRoleRepository->verifyAccessToResourceByNameAndRoles(
            $resourceReference->getResource(),
            $currentRoles
        );

        return $this->previousResults[$token->getUserIdentifier()][$routeName] ??= $result;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return 'resource_route' === strtolower($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        return 'string' === $subjectType;
    }
}