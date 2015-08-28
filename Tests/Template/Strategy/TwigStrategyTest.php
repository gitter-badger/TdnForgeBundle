<?php

namespace Tdn\ForgeBundle\Tests\Template\Strategy;

use Tdn\ForgeBundle\Template\Strategy\TwigTemplateStrategy;
use Tdn\ForgeBundle\TdnForgeBundle;

/**
 * Class TwigStrategyTest
 * @package Tdn\ForgeBundle\Tests\Template\Strategy
 */
class TwigStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $this->assertEquals('hello world', $this->getRendered());
    }

    /**
     * @return TwigTemplateStrategy
     */
    protected function getTemplateStrategy()
    {
        $outputEngine = new TwigTemplateStrategy();
        $outputEngine->addSkeletonDir($this->getSkeletonDir());

        return $outputEngine;
    }

    /**
     * @return string
     */
    private function getSkeletonDir()
    {
        $bundleClass    = new \ReflectionClass(new TdnForgeBundle());
        $skeletonDir = dirname($bundleClass->getFileName()) . '/Tests/Fixtures/skeleton';

        return $skeletonDir;
    }

    private function getRendered()
    {
        return $this->getTemplateStrategy()->render('hello.txt.twig', [
            'hello_var' => 'hello world'
        ]);
    }
}
