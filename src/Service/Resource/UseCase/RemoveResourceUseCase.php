<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Service\Resource\ParentResourceCreator;
use Optime\Acl\Bundle\Service\Resource\UseCase\Exception\DeleteResourceException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

/**
 * @author Manuel Aguirre
 */
class RemoveResourceUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(Resource $resource, bool $flush = true): void
    {
        if (!$resource->isCreatedByUser()) {
            throw new DeleteResourceException(sprintf(
                "The resource '%s' was not been created by user and can not been removed.",
                $resource->getName(),
            ));
        }

        if (0 !== count($resource->getRoles())) {
            throw new DeleteResourceException(sprintf(
                "The resource '%s' has related roles. Please empty roles relations.",
                $resource->getName(),
            ));
        }

        if (0 !== count($resource->getReferences())) {
            throw new DeleteResourceException(sprintf(
                "The resource '%s' has related references. Please empty references relations.",
                $resource->getName(),
            ));
        }

        $this->entityManager->remove($resource);
        $flush and $this->entityManager->flush();
    }
}