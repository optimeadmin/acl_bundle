<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\DependencyInjection;

use Optime\Acl\Bundle\Attribute\Resource;
use Optime\Acl\Bundle\Security\User\RolesProviderInterface;
use Optime\Acl\Bundle\Service\Reference\Loader\DirectoryReferencesLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use function array_map;
use function is_dir;
use function realpath;
use function rtrim;
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

        $container->setParameter('optime_acl.enabled', $config['enabled']);

        $this->configureRolesProvider($config, $container);
        $this->configureResourcesDirs($config, $container);
    }

    private function configureResourcesDirs(array $config, ContainerBuilder $container): void
    {
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
            ->setArgument(1, $config['excluded_resources'])
            ->setArgument(2, new Reference('routing.loader'));
    }

    private function configureRolesProvider(array $config, ContainerBuilder $container): void
    {
        $container->setAlias(RolesProviderInterface::class, $config['roles_provider']);
    }
}