<?php

namespace Tdn\ForgeBundle\Traits;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Interface BundledInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface BundledInterface
{
    /**
     * @return BundleInterface
     */
    public function getBundle();
}
