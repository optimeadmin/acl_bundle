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
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

/**
 * @author Manuel Aguirre
 */
#[AutoconfigureTag("monolog.logger", ['channel' => 'access_control'])]
class UpdateResourcesUseCase
{
    public function __construct(
        private ResourceRepository $repository,
        private EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(FormInterface $form): void
    {
        if (!$form->getConfig()->getType()->getInnerType() instanceof ResourcesConfigType) {
            throw new \InvalidArgumentException(
                "Solo se puede pasar un form de tipo '" . ResourcesConfigType::class . "'"
            );
        }

        /** @var FormInterface $resourceForm */
        foreach ($form->get('resources') as $resourceForm) {
            if ($resourceForm->get('apply')->getData()) {
                $this->updateResource($resourceForm->getData());
            }
        }
    }

    private function updateResource(Resource $resource): void
    {
        $otherResource = $this->repository->findOneByName($resource->getName());

        $this->logger?->debug('Actualizando el recurso {resource}. New name: {new_name}', [
            'resource' => $resource->getId(),
            'new_name' => $resource->getName(),
        ]);

        if ($otherResource && $otherResource->getId() !== $resource->getId()) {
            // hay otro recurso, y es disinto del que se está editando.
            // debemos pasar los datos del recurso que se edita al otro recurso y eliminar el editado.
            // todo: validar que debe pasar con los perfiles.

            $this->logger?->debug(
                'Se encontró otro recurso existente ({resource}) con el mismo nombre, ' .
                'por lo que se pasan todas la referencias a dicho recurso.', [
                'resource' => $otherResource->getId(),
                'references' => $resource->getReferences()->toArray(),
            ]);

            foreach ($resource->getReferences() as $reference) {
                $otherResource->addReference($reference);
                $resource->removeReference($reference);
            }

            $this->entityManager->persist($otherResource);

            if (0 === count($resource->getRoles())) {
                $this->entityManager->remove($resource);

                $this->logger?->debug(
                    'Como el recurso {resource} ya no tiene referencias ni roles asociados,' .
                    ' se elimina de la base de datos!', [
                    'resource' => $otherResource->getId(),
                ]);
            } else {
                $this->logger?->debug(
                    'El recurso {resource} tiene roles asociados, por lo que se mantiene en la base de datos!', [
                    'resource' => $otherResource->getId(),
                ]);
            }
        } else {
            $this->entityManager->persist($resource);
        }

        $this->entityManager->flush();
    }
}