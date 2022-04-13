<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\Security\Voter;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Repository\ResourceRoleRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function dd;
use function func_get_args;

class ResourceVoter extends Voter
{

    private array $previousResults = [];

    public function __construct(
        private RolesProviderInterface $rolesProvider,
        private ResourceRepository $aclResourceRepository,
        private ResourceRoleRepository $aclResourceRoleRepository,
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

//        dump($subject);
        if (isset($this->previousResults[$token->getUserIdentifier()][$subject])) {
            //retornando valor guardado en caché
//            dump('cache');
            return $this->previousResults[$token->getUserIdentifier()][$subject];
        }

//        if(!$token->getUser()){
//            return false;
//        }

        $currentRoles = $this->rolesProvider->getRolesByToken($token);

//        if (!count($currentRoles)) {
//            return false;
//        }

        if (!$aclResource = $this->aclResourceRepository->findOneByName($subject)) {
            return false; //hay que definir que se hará en caso de no conseguir el registro
        }

        $result = $this->aclResourceRoleRepository->verifyAccessToResourceByNameAndRoles($aclResource, $currentRoles);

        $this->processCache($token, $subject, $result);

//        dump($this->previousResults);
//        return true;
        return $result;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return 'resource' === strtolower($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        return 'string' === $subjectType;
    }

    private function processCache(TokenInterface $token, string $resource, bool $result)
    {
        if ($result) {
            foreach ($this->splitDistinctResourceName($resource) as $resourceName) {
                $this->addResultToCache($token->getUserIdentifier(), $resourceName, $result);
            }
        } else {
            $this->addResultToCache($token->getUserIdentifier(), $resource, $result);
        }
    }

    private function addResultToCache(string $userIdentifier, string $resource, bool $result)
    {
        $this->previousResults[$userIdentifier][$resource] = $result;
    }

    private function splitDistinctResourceName(string $resource): array
    {
        if (str_contains($resource, ' ')) {
            $stringCut = substr($resource, 0, strrpos($resource, ' '));
//            dump($resource, strrpos($resource, ' '), $stringCut);
            return [$resource, ...$this->splitDistinctResourceName($stringCut)];
        } else {
            return [$resource];
        }
    }
}