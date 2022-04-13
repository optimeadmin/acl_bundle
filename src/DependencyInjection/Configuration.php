<?php

declare(strict_types=1);

namespace Optime\Acl\Bundle\DependencyInjection;

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
            ->end();

        return $treeBuilder;
    }
}
