<?php
/**
 * @author Manuel Aguirre
 */
declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase\Request;


use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Roles\AppRolesCounter;
use function count;

/**
 * @author Manuel Aguirre
 */
class RequestFactory
{
    public function __construct(
        private ResourceRepository $repository,
        private RolesProviderInterface $rolesProvider,
        private AppRolesCounter $rolesCounter,
    ) {
    }

    public function createResourcesWithRoles(): ResourcesWithRolesRequest
    {
        $resources = [];

        foreach ($this->repository->allVisible() as $resource) {
            $resources[$resource->getId()] = $this->createResourceRequest($resource);
        }

        return new ResourcesWithRolesRequest($resources);
    }

    public function createResourceRequest(Resource $resource): ResourceRequest
    {
        $request = new ResourceRequest($resource);
        $request->roles = $this->rolesProvider->getRolesByResource($resource);
        $request->selectedAll = count($this->rolesCounter) == count($request->roles);

        return $request;
    }
}