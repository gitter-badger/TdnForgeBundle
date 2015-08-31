<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;

/**
 * Interface HasTemplateStrategyInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface HasTemplateStrategyInterface
{
    /**
     * @return TemplateStrategyInterface
     */
    public function getTemplateStrategy();
}
