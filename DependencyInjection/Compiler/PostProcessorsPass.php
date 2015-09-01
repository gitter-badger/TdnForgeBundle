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
        if (!$container->hasDefinition('tdn_forge.writer.strategy.default')
            || !$container->hasDefinition('tdn_forge.writer.postprocessor.chain')
        ) {
            return;
        }

        $postProcessors = $container->findTaggedServiceIds('tdn_forge.writer.postprocessor');
        $definition = $container->getDefinition('tdn_forge.writer.postprocessor.postprocessor_chain');

        foreach (array_keys($postProcessors) as $id) {
            $definition->addMethodCall('addPostProcessor', [new Reference($id)]);
        }
    }
}
