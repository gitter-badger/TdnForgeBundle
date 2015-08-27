<?php

namespace Tdn\ForgeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PostProcessorsPass
 * @package Tdn\ForgeBundle\DependencyInjection\Compiler
 */
class PostProcessorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tdn_forge.template.strategy.default')
            || !$container->hasDefinition('tdn_forge.template.postprocessor.chain')
        ) {
            return;
        }

        $postProcessors = $container->findTaggedServiceIds('tdn_forge.template.postprocessor');
        $definition = $container->getDefinition('tdn_forge.template.postprocessor.postprocessor_chain');

        foreach ($postProcessors as $id => $tags) {
            $definition->addMethodCall('addPostProcessor', [new Reference($id)]);
        }
    }
}
