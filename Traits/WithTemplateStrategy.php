<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Trait WithTemplateStrategy
 * @package Tdn\ForgeBundle\Traits
 */
trait WithTemplateStrategy
{
    /**
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @param TemplateStrategyInterface $templateStrategy
     */
    protected function setTemplateStrategy(TemplateStrategyInterface $templateStrategy)
    {
        $this->templateStrategy = $templateStrategy;
    }

    /**
     * @return TemplateStrategyInterface
     */
    public function getTemplateStrategy()
    {
        return $this->templateStrategy;
    }
}
