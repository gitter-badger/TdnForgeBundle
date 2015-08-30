<?php

namespace Tdn\ForgeBundle\Tests\Traits;

use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Template\Strategy\TwigTemplateStrategy;
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
        $templateStrategy = Mockery::mock(new TwigTemplateStrategy());
        $templateStrategy->shouldDeferMissing();

        return $templateStrategy;
    }
}
