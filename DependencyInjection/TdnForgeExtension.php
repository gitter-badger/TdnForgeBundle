<?php

namespace Tdn\ForgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TdnForgeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $this->addTemplateOverrides($config, $container);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('template-postprocessors.yml');
        $loader->load('template-strategies.yml');
        $loader->load('doctrine.yml');
        $loader->load('symfony.yml');
        $loader->load('generator.yml');

    }

    protected function addTemplateOverrides(array $config, ContainerBuilder $container)
    {
        if (isset($config['skeleton_overrides'])) {
            $container->set('tdn_forge.template.strategy.skeleton_dir_overrides', $config['skeleton_overrides']);
        }
    }

    public function getAlias()
    {
        return 'tdn_forge';
    }
}
