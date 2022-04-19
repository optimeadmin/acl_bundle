<?php

namespace Optime\Acl\Bundle\Service\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Attribute\Resource;
use Optime\Acl\Bundle\Entity\Resource as ResourceEntity;
use Optime\Acl\Bundle\Repository\ResourceReferenceRepository;
use Optime\Acl\Bundle\Repository\ResourceRepository;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ResourceLoader
{
    private ServiceLocator $resources;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResourceRepository $repository,
        private ResourceReferenceRepository $referenceRepository,
        #[TaggedLocator('optime_acl.resource')] ServiceLocator $resources
    ) {
        $this->resources = $resources;
    }

    public function process()
    {
        $references = $this->resources->getProvidedServices();
        foreach ($references as $reference) {
            $reflection = new ReflectionClass($reference);
            $resourcesAttr = $this->extractResources($reflection);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
                $resourcesAttr = [...$resourcesAttr, ...$this->extractResources($reflectionMethod)];
            }
            $this->saveResources($resourcesAttr);
        }
    }

    private function extractResources(ReflectionClass|ReflectionMethod $reflection): array
    {
        $resources = [];
        $resourcesAttr = $reflection->getAttributes(Resource::class);
        foreach ($resourcesAttr as $reflectionAttribute) {
            $resources[] = $reflectionAttribute->newInstance()->setReference(
                $reflection instanceof ReflectionClass
                    ? $reflection->getName()
                    : $reflection->class.'::'.$reflection->getName()
            );
        }

        return $resources;
    }

    private function saveResources(array $resources)
    {
        foreach ($resources as $resource) {
            dd($resource);
            if (!$resourceName = $resource->getResource() || !$reference = $resource->getReference()) {
                throw new \LogicException('Los valores de resource y reference');
            }
            if ($resourceEntity = $this->repository->findOneByName($resourceName)) {
                //existe resource
                if (!$this->referenceRepository->hasByResourceAndReference($resourceName, $reference)) {
                    $resourceEntity->addReference($reference);
                }
                //si la referencia esta en otro lado
            } else {
                //no existe resource
                if ($referenceEntity = $this->referenceRepository->findOneByReference($reference)) {
                    // se encontro una referencia hay que decirdir si cambia le nombre o eliminarla
                    $otherResourceEntity = $referenceEntity->getResource();
                    if (1 === count($otherResourceEntity->getReferences())) {
                        //solo hay una referencia por tanto se cambiará el nombre
                        $otherResourceEntity->changeName($resourceName);
                    } else {
                        foreach ($otherResourceEntity->getReferences() as $currentReference) {

                        }
                    }
                } else {
                    // agregar recurso y referencia nuevos
                    $resourceEntity = ResourceEntity::createFromAttribute($resource);
                }
            }
            $this->entityManager->persist($resourceEntity);

            // no recurso, si referencia.
            // otro recurso, tienes otras referencias

            if (!$this->referenceRepository->hasByResourceAndReference($resourceName, $reference)) {

            }
            dump(
                $resource->getResource(),
                $resource->getReference()
            );
        }
    }
}