<?php

namespace Optime\Acl\Bundle\Service\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Optime\Acl\Bundle\Attribute\Resource;
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
        private ResourceRepository     $repository,
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
            dump($resourcesAttr);
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
}