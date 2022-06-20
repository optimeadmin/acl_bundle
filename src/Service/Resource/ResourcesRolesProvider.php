<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource;

use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;

/**
 * @author Manuel Aguirre
 */
class ResourcesRolesProvider
{
    public function __construct(
        private ResourceRepository $repository,
        private RolesProviderInterface $rolesProvider,
    ) {
    }

    public function getAll(): array
    {
        $resources = $this->repository->allVisible();
        $data = [];
        $childItems = [];

        foreach ($resources as $resource) {
//            $roles = $this->rolesProvider->getRolesByResource($resource);
            $data[$resource->getName()] = new RolesByResource($resource, []);

            if ($resource->hasParent()) {
                $childItems[$resource->getParent()][$resource->getName()] ??= $resource->getName();
            }
        }

        foreach ($data as $resourceName => $item) {
            if (isset($childItems[$resourceName])) {
                $item->setChildren($childItems[$resourceName]);
            }
        }

        return $data;
    }
}