<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface as ITemplateStrategy;

/**
 * Trait TemplateStrategy
 * @package Tdn\ForgeBundle\Traits
 */
trait TemplateStrategy
{
    /**
     * @var ITemplateStrategy
     */
    private $templateStrategy;

    /**
     * @param ITemplateStrategy $templateStrategy
     */
    protected function setTemplateStrategy(ITemplateStrategy $templateStrategy)
    {
        $this->templateStrategy = $templateStrategy;
    }

    /**
     * @return ITemplateStrategy
     */
    public function getTemplateStrategy()
    {
        return $this->templateStrategy;
    }
}
