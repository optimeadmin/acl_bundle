<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Manuel Aguirre
 */
#[AutoconfigureTag("monolog.logger", ['channel' => 'access_control'])]
class ParentResourceCreator
{
    public function __construct(
        private ResourceRepository $repository,
        private EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function createIfApply(Resource $resource): void
    {
        if (!$resource->hasParent()) {
            return;
        }

        $parentName = $resource->getParent();

        if (!$parent = $this->repository->findOneByName($parentName)) {
            $this->logger?->debug('El recurso {resource} debe tener un padre y este no existe en la BD, '
                . 'por lo que se crea el nuevo recurso padre {parent}', [
                'resource' => $resource->getName(),
                'parent' => $parentName,
            ]);

            $parent = new Resource($parentName, null, $resource->isCreatedByUser());
            $this->entityManager->persist($parent);
            $this->entityManager->flush();
        }

        $this->createIfApply($parent);
    }
}