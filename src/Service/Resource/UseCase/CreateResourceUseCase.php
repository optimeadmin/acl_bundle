<?php
/**
 * @author Manuel Aguirre
 */

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Service\Resource\ParentResourceCreator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

/**
 * @author Manuel Aguirre
 */
class CreateResourceUseCase
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private ParentResourceCreator $parentResourceCreator,
    ) {
    }

    public function handle(Resource $resource, bool $flush = true): void
    {
        $errors = $this->validator->validate($resource);

        if (0 !== count($errors)) {
            throw new LogicException($errors[0]->getMessage());
        }

        $this->entityManager->persist($resource);
        $flush and $this->entityManager->flush();

        $this->parentResourceCreator->createIfApply($resource);
    }
}