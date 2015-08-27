<?php

namespace Tdn\ForgeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SkeletonOverridesPass
 * @package Tdn\ForgeBundle\DependencyInjection\Compiler
 */
class SkeletonOverridesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('tdn_forge.template.strategy.skeleton_dir_overrides')
            || !$container->hasDefinition('tdn_forge.template.strategy.default')
        ) {
            return;
        }

        $skeletonDirs = $container->getParameter('tdn_forge.template.strategy.skeleton_dir_overrides');
        $definition = $container->getDefinition('tdn_forge.template.strategy.default');

        foreach ($skeletonDirs as $skeletonDir) {
            $definition->addMethodCall('addSkeletonDir', [$skeletonDir]);
        }
    }
}
