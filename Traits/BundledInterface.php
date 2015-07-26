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
     * @param BundleInterface $bundle
     */
    public function setBundle(BundleInterface $bundle);

    /**
     * @return BundleInterface
     */
    public function getBundle();
}
