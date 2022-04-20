<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Reference\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Form\Type\Config\ReferencesConfigType;
use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Reference\Loader\LoadedReference;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

/**
 * @author Manuel Aguirre
 */
#[AutoconfigureTag("monolog.logger", ['channel' => 'access_control'])]
class SaveReferencesUseCase
{
    public function __construct(
        private ResourceRepository $resourceRepository,
        private ResourceReferenceRepository $referenceRepository,
        private EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(FormInterface $form): void
    {
        if (!$form->getConfig()->getType()->getInnerType() instanceof ReferencesConfigType) {
            throw new \InvalidArgumentException(
                "Solo se puede pasar un form de tipo '" . ReferencesConfigType::class . "'"
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
        $existentReference = $this->referenceRepository->byName($referenceName);

        if ($loadedReference->isActive()) {
            if ($existentReference) {
                $this->logger?->debug('Actualizando la referencia {reference} del recurso {resource}', [
                    'reference' => $loadedReference->getName(),
                    'resource' => $resourceName,
                ]);
            } else {
                $this->logger?->debug('Creando la referencia {reference} en el recurso {resource}', [
                    'reference' => $loadedReference->getName(),
                    'resource' => $resourceName,
                ]);
            }
        } elseif ($resourceName !== LoadedReference::HIDDEN) {
            // referencia que estaba oculta y se vuelve a tener en cuenta
            $this->logger?->debug('Reactivando la referencia {reference} en el recurso {resource}', [
                'reference' => $loadedReference->getName(),
                'resource' => $resourceName,
            ]);
        }

        if ($existentReference) {
            $otherResource = $existentReference->getResource();

            if ($otherResource->getName() !== $resourceName) {
                $otherResource->removeReference($referenceName);
                $this->entityManager->persist($otherResource);

                $this->logger?->debug(
                    'La referencia estaba asociada al recurso #{resource_id} => {resource_name}, ' .
                    'por lo que se quita de allí y se pasa al nuevo recurso {new_resource}',
                    [
                        'resource_id' => $otherResource->getId(),
                        'resource_name' => $otherResource->getName(),
                        'new_resource' => $resourceName,
                    ]
                );
            }
        }

        if (!$resource) {
            $resource = new Resource($resourceName, $referenceName);

            $this->logger?->debug('El recurso {resource_name} no existe, por lo que se crea.', [
                'resource_name' => $resourceName,
            ]);
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
        $this->logger?->debug('Ocultando la referencia {reference}', [
            'reference' => $loadedReference->getName(),
        ]);

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
            $this->logger?->debug('El recurso {resource} debe tener un padre y este no existe en la BD, '
                . 'por lo que se crea el nuevo recurso padre {parent}', [
                'resource' => $resource->getName(),
                'parent' => $parentName,
            ]);

            $parent = new Resource($parentName);
            $this->entityManager->persist($parent);
            $this->entityManager->flush();
        }

        $this->createParentResourcesIfApply($parent);
    }
}