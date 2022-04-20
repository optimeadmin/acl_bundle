<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Form\Type\ResourcesConfigType;
use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;
use Symfony\Component\Form\FormInterface;

/**
 * @author Manuel Aguirre
 */
class SaveReferencesUseCase
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private ResourceReferenceRepository $referenceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(FormInterface $form): void
    {
        if (!$form->getConfig()->getType()->getInnerType() instanceof ResourcesConfigType) {
            throw new \InvalidArgumentException(
                "Solo se puede pasar un form de tipo '" . ResourcesConfigType::class . "'"
            );
        }

        $this->entityManager->beginTransaction();

        /** @var FormInterface $reference */
        foreach ($form->get('references') as $reference) {
            if ($reference->get('hide')->getData()) {
                $this->hideReference($reference->getData());
            } elseif ($reference->get('apply')->getData()) {
                $this->saveReference($reference->getData());
            }
        }

        $this->entityManager->commit();
    }

    private function saveReference(LoadedReference $loadedReference): void
    {
        $referenceName = $loadedReference->getName();
        $resourceName = $loadedReference->getModifiedResourceName();
        $resource = $this->resourceRepository->findOneByName($resourceName);

        if ($existentReference = $this->referenceRepository->byName($referenceName)) {
            $otherResource = $existentReference->getResource();

            if ($otherResource->getName() !== $resourceName) {
                $otherResource->removeReference($referenceName);
                $this->entityManager->persist($otherResource);
            }
        }

        if (!$resource) {
            $resource = new Resource($resourceName, $referenceName);
        }

        $resource->addReference($referenceName);
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        if ($loadedReference->isActive()) {
            $this->createParentResourcesIfApply($resource);
        }
    }

    private function hideReference(LoadedReference $loadedReference): void
    {
        $loadedReference->setModifiedResourceName(LoadedReference::HIDDEN);
        $this->saveReference($loadedReference);
    }

    private function createParentResourcesIfApply(Resource $resource): void
    {
        if (!$resource->hasParent()) {
            return;
        }

        $parentName = $resource->getParent();

        if (!$parent = $this->resourceRepository->findOneByName($parentName)) {
            $parent = new Resource($parentName);
            $this->entityManager->persist($parent);
            $this->entityManager->flush();
        }

        $this->createParentResourcesIfApply($parent);
    }
}