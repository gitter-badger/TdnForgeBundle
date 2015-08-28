<?php

namespace Tdn\ForgeBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tdn\ForgeBundle\DependencyInjection\Compiler\PostProcessorsPass;
use Tdn\ForgeBundle\DependencyInjection\Compiler\SkeletonOverridesPass;

/**
 * Class TdnForgeBundle
 * @package Tdn\ForgeBundle
 */
class TdnForgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SkeletonOverridesPass());
        $container->addCompilerPass(new PostProcessorsPass());
    }
}
