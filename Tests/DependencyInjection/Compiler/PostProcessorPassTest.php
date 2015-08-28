<?php

namespace Tdn\ForgeBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use \Mockery;
use Tdn\ForgeBundle\DependencyInjection\Compiler\PostProcessorsPass;

/**
 * Class PostProcessorPassTest
 * @package Tdn\ForgeBundle\Tests\DependencyInjection\Compiler
 */
class PostProcessorPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PostProcessorsPass
     */
    private $pass;

    protected function setUp()
    {
        $this->pass = new PostProcessorsPass();
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
                'findTaggedServiceIds'  => $this->getTaggedServiceIds(),
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
            ->times(count($this->getTaggedServiceIds()))
            ->withAnyArgs()
        ;

        return $mock;
    }

    /**
     * @return array
     */
    private function getTaggedServiceIds()
    {
        return [
            'foo' => [],
            'bar' => [],
            'baz' => []
        ];
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
