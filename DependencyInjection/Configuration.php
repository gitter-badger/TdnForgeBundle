<?php

namespace Tdn\ForgeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Tdn\ForgeBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('tdn_forge');

        $rootNode
            ->children()
                ->arrayNode('skeleton_overrides')
                ->addDefaultChildrenIfNoneSet()
                ->prototype('scalar')
                ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
