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
use Optime\Acl\Bundle\Service\Reference\UseCase\Request\ReferencesRequest;
use Optime\Acl\Bundle\Service\Resource\ParentResourceCreator;
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
        private ParentResourceCreator $parentResourceCreator,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(ReferencesRequest $request): void
    {
        $this->entityManager->beginTransaction();

        foreach ($request->appliedReferences as $reference) {
            $this->saveReference($reference);
        }

        foreach ($request->hiddenReferences as $reference) {
            $this->hideReference($reference);
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
            $this->parentResourceCreator->createIfApply($resource);
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
}