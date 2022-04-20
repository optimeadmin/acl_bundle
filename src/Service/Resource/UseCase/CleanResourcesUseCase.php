<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Manuel Aguirre
 */
#[AutoconfigureTag("monolog.logger", ['channel' => 'access_control'])]
class CleanResourcesUseCase
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(): void
    {
        $resources = $this->resourceRepository->getUnused();
        $hasDeletedItems = false;

        $this->logger?->debug('Ejecutando cleanup de recursos!!!');

        foreach ($resources as $resource) {
            if ($this->resourceRepository->hasChildren($resource)) {
                continue;
            }

            $this->logger?->debug(
                'Se elimina el recurso {resource} ya que no tiene referencias ni roles asociados,' .
                ' además, no tiene recursos "hijos" que dependan de él.',
                [
                    'resource' => $resource->getId(),
                ]
            );

            $hasDeletedItems = true;
            $this->entityManager->remove($resource);
            $this->entityManager->flush();
        }

        if (!$hasDeletedItems) {
            $this->logger?->debug('No se eliminó ningún recurso, ya que todos se están usando!!!');
        }
    }
}