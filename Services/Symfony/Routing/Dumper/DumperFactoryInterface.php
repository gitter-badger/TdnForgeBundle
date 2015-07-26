<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Dumper;

use Doctrine\Common\Collections\Collection;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tuck\ConverterBundle\Exception\UnknownFormatException;
use Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\DumperInterface;

/**
 * Interface DumperFactoryInterface
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Dumper
 */
interface DumperFactoryInterface
{
    /**
     * @param string $type
     * @param Collection|RouteDefinition[] $routeCollection
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    public function createDumper($type, Collection $routeCollection);
}
