<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesRolesRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesWithRolesRequest;
use function dd;

/**
 * @author Manuel Aguirre
 */
class UpdateAllResourcesRolesUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UpdateRolesUseCase $updateProfilesUseCase,
        ResourceRepository $resourceRepository,
    ) {
    }

    public function handle(ResourcesWithRolesRequest $request): void
    {
        $this->entityManager->beginTransaction();

        foreach ($request->resources as $resource) {
            $this->updateProfilesUseCase->handle($resource, false);
        }

        $this->entityManager->flush();
        $this->entityManager->commit();
    }

    public function handleApi(ResourcesRolesRequest $request): void
    {
        foreach ($request->resources as $resource) {
            dump($resource['name']);
        }
    }

    private function loadResource()
    {

    }
}