<?php

namespace Tdn\ForgeBundle\Tests\Traits;

use Tdn\ForgeBundle\Template\PostProcessor\PsrPostProcessor;
use Tdn\ForgeBundle\Template\PostProcessor\PostProcessorInterface;
use Tdn\ForgeBundle\Template\PostProcessor\PostProcessorChain;
use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Template\Strategy\TwigStrategy;
use \Mockery;

/**
 * Trait TwigStrategyMock
 * @package Tdn\ForgeBundle\Tests\Traits
 */
trait TwigStrategyMock
{
    /**
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @return TemplateStrategyInterface
     */
    public function getTemplateStrategy()
    {
        if (null === $this->templateStrategy) {
            $this->templateStrategy = $this->createTemplateStrategy();
        }

        return $this->templateStrategy;
    }

    /**
     * @return TemplateStrategyInterface
     */
    protected function createTemplateStrategy()
    {
        $templateStrategy = Mockery::mock(new TwigStrategy($this->getPostProcessorChain()));
        $templateStrategy->shouldDeferMissing();

        return $templateStrategy;
    }

    /**
     * @return PostProcessorChain
     */
    private function getPostProcessorChain()
    {
        $postProcessorChain = new PostProcessorChain();
        foreach ($this->getPostProcessors() as $postProcessor) {
            $postProcessorChain->addPostProcessor($postProcessor);
        }

        return $postProcessorChain;
    }

    /**
     * @return array|PostProcessorInterface
     */
    private function getPostProcessors()
    {
        return [
            new PsrPostProcessor($this->getKernelRootDirCompatible())
        ];
    }

    /**
     * @return string
     */
    private function getKernelRootDirCompatible()
    {
        return '/vagrant/vendor/tdn/forgebundle/bin';
    }
}
