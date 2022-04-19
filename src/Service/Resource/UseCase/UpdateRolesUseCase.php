<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use function dd;
use function dump;

/**
 * @author Manuel Aguirre
 */
class UpdateRolesUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function handle(ResourceRequest $request, bool $flush = true): void
    {
        $resource = $request->getResource();
        $this->propertyAccessor->setValue($resource, 'roles', $request->roles);

        $this->entityManager->persist($resource);
        $flush and $this->entityManager->flush();
    }
}