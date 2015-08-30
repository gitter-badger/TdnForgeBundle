<?php

namespace Tdn\ForgeBundle\Traits;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Trait Bundled
 * @package Tdn\ForgeBundle\Traits
 */
trait Bundled
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @param BundleInterface $bundle
     */
    protected function setBundle(BundleInterface $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return BundleInterface
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
