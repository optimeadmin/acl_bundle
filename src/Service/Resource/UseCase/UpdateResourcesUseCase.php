<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Service\Resource\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Entity\Resource;
use Optime\Acl\Bundle\Form\Type\Config\ResourcesConfigType;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use Optime\Acl\Bundle\Service\Resource\ParentResourceCreator;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourceRequest;
use Optime\Acl\Bundle\Service\Resource\UseCase\Request\ResourcesConfigRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;
use function strlen;

/**
 * @author Manuel Aguirre
 */
#[AutoconfigureTag("monolog.logger", ['channel' => 'access_control'])]
class UpdateResourcesUseCase
{
    public function __construct(
        private ResourceRepository $repository,
        private EntityManagerInterface $entityManager,
        private ParentResourceCreator $parentResourceCreator,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(ResourcesConfigRequest $configRequest): void
    {
        /** @var ResourceRequest $resourceRequest */
        foreach ($configRequest->resources as $resourceRequest) {
            if (strlen($resourceRequest->name) < 3) {
                continue;
            }

            $this->updateResource($resourceRequest);
        }
    }

    private function updateResource(ResourceRequest $resourceRequest): void
    {
        $resource = $resourceRequest->getResource();
        $newName = $resourceRequest->name;

        $otherResource = $this->repository->findOneByName($newName);

        $this->logger?->debug('Actualizando el recurso {resource}. New name: {new_name}', [
            'resource' => $resource->getId(),
            'new_name' => $newName,
        ]);

        if ($otherResource && $otherResource->getId() !== $resource->getId()) {

            if (null === $resource->getId()) {
                // Ya existe otro recurso con este nombre, por lo que
                // no hace falta crear ningun recurso nuevo ni actualizar el otro.

                $this->logger?->debug(
                    'Se encontró otro recurso existente ({resource}) con el mismo nombre, ' .
                    'por lo que se evita crear el nuevo recurso', [
                    'resource' => $otherResource->getId(),
                ]);

                return;
            }

            // hay otro recurso, y es distinto del que se está editando.
            // debemos pasar los datos del recurso que se edita al otro recurso y eliminar el editado.

            $this->logger?->debug(
                'Se encontró otro recurso existente ({resource}) con el mismo nombre, ' .
                'por lo que se pasan todas la referencias y roles a dicho recurso.', [
                'resource' => $otherResource->getId(),
                'references' => $resource->getReferences()->toArray(),
                'roles' => $resource->getRoles()->toArray(),
            ]);

            foreach ($resource->getReferences() as $reference) {
                $otherResource->addReference($reference);
                $resource->removeReference($reference);
            }

            foreach ($resource->getRoles() as $role) {
                $otherResource->addRole($role);
                $resource->removeRole($role);
            }

            $this->entityManager->persist($otherResource);

            $this->entityManager->remove($resource);

            $this->logger?->debug(
                'Como el recurso {resource} ya no tiene referencias ni roles asociados,' .
                ' se elimina de la base de datos!', [
                'resource' => $resource->getId(),
            ]);
        } else {
            $resource->setName($newName);
            $resource->setDescription($resourceRequest->description);

            $this->entityManager->persist($resource);
            $this->entityManager->flush();
            $this->parentResourceCreator->createIfApply($resource);
        }

        $this->entityManager->flush();
    }
}