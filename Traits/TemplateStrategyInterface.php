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
     * @return TemplateStrategy
     */
    public function getTemplateStrategy();
}
