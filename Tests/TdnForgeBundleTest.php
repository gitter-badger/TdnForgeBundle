<?php

namespace Tdn\ForgeBundle\Tests;

use \Mockery;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tdn\ForgeBundle\TdnForgeBundle;

/**
 * Class TdnForgeBundleTest
 * @package Tdn\ForgeBundle\Tests
 */
class TdnForgeBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $container = Mockery::mock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $container
            ->shouldIgnoreMissing()
            ->shouldReceive('addCompilerPass')
            ->with(Mockery::type('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface'))
            ->atLeast()
            ->times(2)
            ->andReturn()
        ;

        $bundle = new TdnForgeBundle();
        /** @var ContainerBuilder $container */
        $bundle->build($container);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
