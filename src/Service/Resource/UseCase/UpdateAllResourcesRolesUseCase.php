<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
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
}