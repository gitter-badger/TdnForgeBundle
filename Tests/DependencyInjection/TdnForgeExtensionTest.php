<?php

namespace Tdn\ForgeBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;
use Tdn\ForgeBundle\DependencyInjection\TdnForgeExtension;

/**
 * Class TdnForgeExtensionTest
 * @package Tdn\ForgeBundle\Tests\DependencyInjection
 */
class TdnForgeExtensionTest extends \PHPUnit_Framework_TestCase
{
    const EMPTY_CONFIG = <<<EOF
skeleton_overrides: ~
EOF;

    const FULL_CONFIG = <<<EOF
skeleton_overrides:
    - 'TestDir1'
    - 'TestDir2'
    - 'TestDir3'
EOF;

    /**
     * @var TdnForgeExtension
     */
    private $loader;

    protected function setUp()
    {
        $this->loader = new TdnForgeExtension();
    }

    public function testDefaults()
    {
        $configuration = new ContainerBuilder();
        $this->loader->load([$this->getConfig(self::EMPTY_CONFIG)], $configuration);
        $this->assertParameter(
            $configuration,
            [],
            'tdn_forge.template.strategy.skeleton_dir_overrides'
        );
        $this->assertNonConfigurableDefaults($configuration);
    }

    public function testOverrides()
    {
        $skeletonDirs = [
            'TestDir1',
            'TestDir2',
            'TestDir3'
        ];

        $configuration = new ContainerBuilder();
        $this->loader->load([$this->getConfig(self::FULL_CONFIG)], $configuration);
        $this->assertParameter(
            $configuration,
            $skeletonDirs,
            'tdn_forge.template.strategy.skeleton_dir_overrides'
        );
        $this->assertNonConfigurableDefaults($configuration);
    }

    private function assertNonConfigurableDefaults(ContainerBuilder $configuration)
    {
        $this->assertHasDefinition('tdn_forge.doctrine.entity.helper', $configuration);
        $this->assertHasDefinition('tdn_forge.generator.factory.standard_generator_factory', $configuration);
        $this->assertHasDefinition('tdn_forge.symfony.service.manager', $configuration);
        $this->assertHasDefinition('tdn_forge.symfony.routing.manager', $configuration);
        $this->assertHasDefinition('tdn_forge.writer.strategy.default', $configuration);
        $this->assertHasDefinition('tdn_forge.writer.postprocessor.psr_postprocessor', $configuration);
        $this->assertHasDefinition('tdn_forge.writer.postprocessor.abstract_postprocessor', $configuration);
        $this->assertHasDefinition('tdn_forge.writer.postprocessor.postprocessor_chain', $configuration);
        $this->assertHasDefinition('tdn_forge.template.strategy.default', $configuration);
    }

    /**
     * @param string $yml
     *
     * @return array
     */
    private function getConfig($yml)
    {
        $parser = new Parser();

        return $parser->parse($yml);
    }

    /**
     * @param ContainerBuilder $configuration
     * @param mixed $value
     * @param string $key
     */
    private function assertParameter(ContainerBuilder $configuration, $value, $key)
    {
        $this->assertEquals($value, $configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     * @param ContainerBuilder $configuration
     */
    private function assertHasDefinition($id, ContainerBuilder $configuration)
    {
        $this->assertTrue(($configuration->hasDefinition($id) ?: $configuration->hasAlias($id)));
    }
}
