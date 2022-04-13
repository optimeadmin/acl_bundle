<?php

namespace Optime\Acl\Bundle\Service\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Attribute\Resource;
use Optime\Acl\Bundle\Entity\Resource as AclResource;
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
        private EntityManagerInterface      $entityManager,
        private ResourceRepository          $repository,
        private ResourceReferenceRepository $referenceRepository,
                                            #[TaggedLocator('optime_acl.resource')] ServiceLocator $resources
    )
    {
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
            $this->processResources($resourcesAttr);
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
                    : $reflection->class . '::' . $reflection->getName()
            );
        }
        return $resources;
    }

    private function processResources(array $resources)
    {
        foreach ($resources as $resource) {
            $aclResource = $this->repository->findOneByName($resource->getResource()) ?? new AclResource($resource->getResource(),true );
            $this->entityManager->persist($aclResource);
            if (!$this->referenceRepository->verifyIfExistByResourceNameAndStringReference($resource->getResource(), $resource->getReference())) {

            }
            dump(
                $resource->getResource(),
                $resource->getReference()
            );
        }
    }
}