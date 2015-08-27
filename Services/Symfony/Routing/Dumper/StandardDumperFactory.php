<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Dumper;

use Doctrine\Common\Collections\Collection;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\DumperInterface;
use Tuck\ConverterBundle\Exception\UnknownFormatException;

/**
 * Class StandardDumperFactory
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Dumper
 */
class StandardDumperFactory implements DumperFactoryInterface
{
    protected $dumperMap = [
        Format::XML  => '\Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\XmlDumper',
        Format::YAML => '\Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\YamlDumper'
    ];

    /**
     * @param string $type
     * @param Collection|RouteDefinition[] $routeCollection
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    public function createDumper($type, Collection $routeCollection)
    {
        $class = $this->getClassFromType($type);

        return new $class($routeCollection);
    }

    /**
     * @param string $type
     *
     * @return DumperInterface
     *
     * @throws UnknownFormatException
     */
    protected function getClassFromType($type)
    {
        if (!isset($this->dumperMap[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->dumperMap[strtolower($type)];
    }
}
