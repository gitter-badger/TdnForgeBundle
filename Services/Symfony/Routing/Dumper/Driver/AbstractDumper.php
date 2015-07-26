<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class AbstractDumper
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver
 */
abstract class AbstractDumper
{
    /**
     * @var ArrayCollection|RouteDefinition[]
     */
    protected $routeCollection;

    public function __construct(ArrayCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }
}
