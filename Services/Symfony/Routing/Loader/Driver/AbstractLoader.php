<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * Class AbstractLoader
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver
 */
abstract class AbstractLoader
{
    /**
     * @var FileLocatorInterface
     */
    protected $locator;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->locator = $fileLocator;
    }
}
