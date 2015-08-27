<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Interface LoaderInterface
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver
 */
interface LoaderInterface
{
    /**
     * @param $resource
     * @return ArrayCollection|RouteDefinition[]
     */
    public function load($resource);
}
