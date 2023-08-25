<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\DependencyInjection;

use Optime\Acl\Bundle\Security\User\DefaultRolesProvider;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('optime_acl');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->canBeDisabled()
            ->children()
                ->booleanNode('cache_voters')->defaultTrue()->end()
                ->arrayNode('header')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('title')->defaultValue('Access Control Backend')->end()
                        ->scalarNode('path')->defaultValue('optime_acl_configuration')->end()
                    ->end()
                ->end()
                ->scalarNode('roles_provider')
                    ->cannotBeEmpty()
                    ->defaultValue(DefaultRolesProvider::class)
                ->end()
                ->arrayNode('resources')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('excluded_resources')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
