<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface as TemplateStrategy;

/**
 * Interface TemplateStrategyInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface TemplateStrategyInterface
{
    /**
     * @param TemplateStrategy $templateStrategy
     */
    public function setTemplateStrategy(TemplateStrategy $templateStrategy);

    /**
     * @return TemplateStrategy
     */
    public function getTemplateStrategy();
}
