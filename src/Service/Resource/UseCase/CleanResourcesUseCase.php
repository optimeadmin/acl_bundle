<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Repository\ResourceRepository;

/**
 * @author Manuel Aguirre
 */
class CleanResourcesUseCase
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(): void
    {
        $resources = $this->resourceRepository->getUnused();

        foreach ($resources as $resource) {
            if ($this->resourceRepository->hasChildren($resource)) {
                continue;
            }

            $this->entityManager->remove($resource);
            $this->entityManager->flush();
        }
    }
}