<?php

namespace Tdn\ForgeBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use \Mockery;
use Tdn\ForgeBundle\DependencyInjection\Compiler\SkeletonOverridesPass;

/**
 * Class SkeletonOverridesPassTest
 * @package Tdn\ForgeBundle\Tests\DependencyInjection\Compiler
 */
class SkeletonOverridesPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SkeletonOverridesPass
     */
    private $pass;

    protected function setUp()
    {
        $this->pass = new SkeletonOverridesPass();
    }

    public function testProcessShouldCompile()
    {
        $this->pass->process($this->createContainerMock());
    }

    public function testProcessShouldDoNothing()
    {
        $this->pass->process(new ContainerBuilder());
    }

    /**
     * @return ContainerBuilder
     */
    private function createContainerMock()
    {
        $container = Mockery::mock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $container
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'hasParameter'  => true,
                'hasDefinition' => true,
                'getParameter'  => $this->getSkeletonDirs(),
                'getDefinition' => $this->createTemplateStrategyDefinitionMock()
            ])
            ->with(Mockery::type('string'))
        ;

        return $container;
    }

    /**
     * @return Definition
     */
    private function createTemplateStrategyDefinitionMock()
    {
        $mock = Mockery::mock('\Symfony\Component\DependencyInjection\Definition');
        $mock
            ->shouldIgnoreMissing()
            ->shouldReceive('addMethodCall')
            ->atLeast()
            ->times(count($this->getSkeletonDirs()))
            ->withAnyArgs()
        ;

        return $mock;
    }

    /**
     * @return array
     */
    private function getSkeletonDirs()
    {
        return [
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Foo',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Bar',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Baz'
        ];
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
