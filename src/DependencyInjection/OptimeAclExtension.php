<?php

namespace Optime\Acl\Bundle\DependencyInjection;

use Optime\Acl\Bundle\Attribute\Resource;
use Optime\Acl\Bundle\Service\Reference\Loader\DirectoryReferencesLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use function array_map;
use function is_dir;
use function realpath;
use function trim;

class OptimeAclExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');

        $container->registerAttributeForAutoconfiguration(Resource::class,
            function (ChildDefinition $definition) {
                $definition->addTag('optime_acl.resource');
            });

        $container->setParameter('optime_acl.enabled', $config['enabled']);
        $container->addObjectResource($this);

        $projectDir = rtrim($container->getParameter('kernel.project_dir'));
        $resources = array_map(function ($dir) use ($projectDir) {
            $path = $projectDir . '/' . trim($dir);

            if (!is_dir($path)) {
                throw new InvalidArgumentException("No existe el directorio '" . $path .
                    "'. Revisar la configuración del bundle de ACL");
            }

            return realpath($path);
        }, $config['resources']);

        $container->findDefinition(DirectoryReferencesLoader::class)
            ->setArgument(0, $resources)
            ->setArgument(1, new Reference('routing.loader'));
    }
}