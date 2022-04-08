<?php

namespace Optime\Acl\Bundle\DependencyInjection;

use Optime\Acl\Bundle\Attribute\Resource;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OptimeAclExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');

        $container->registerAttributeForAutoconfiguration(Resource::class, function (ChildDefinition $definition, Resource $attribute){
            $definition->addTag('optime_acl.resource');
        });
    }
}