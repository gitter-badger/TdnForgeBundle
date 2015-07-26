<?php

namespace Tdn\ForgeBundle\Services\Symfony;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\StandardDumperFactory;
use Tdn\ForgeBundle\Services\Symfony\Routing\Loader\StandardLoaderFactory;

/**
 * Class RoutingManager
 * @package Tdn\ForgeBundle\Services\Symfony
 */
class RoutingManager extends AbstractFileUtils
{
    /**
     * @var ArrayCollection|RouteDefinition[]
     */
    protected $routeDefinitions;

    public function __construct()
    {
        $this->routeDefinitions = new ArrayCollection();
    }

    /**
     * Note: Adding an already existing definition overrides it.
     *
     * @param RouteDefinition $definition
     *
     * @return $this
     */
    public function addRouteDefinition(RouteDefinition $definition)
    {
        $this->routeDefinitions->add($definition);

        return $this;
    }

    /**
     * @return ArrayCollection|RouteDefinition[]
     */
    public function getRouteDefinitions()
    {
        return $this->routeDefinitions;
    }

    /**
     * @param string $file
     * @return string
     */
    public function dump($file)
    {
        $file = new File($file);
        $routeCollection = $this->getResolvedDefinitions($file);

        return $this->getDumperFactory()->createDumper($this->getFormat($file), $routeCollection)->dump();
    }

    /**
     * @return StandardDumperFactory
     */
    protected function getDumperFactory()
    {
        return new StandardDumperFactory();
    }

    /**
     * @return StandardLoaderFactory
     */
    protected function getLoaderFactory()
    {
        return new StandardLoaderFactory();
    }

    /**
     * @param File $file
     * @return ArrayCollection|RouteDefinition[]
     */
    protected function getResolvedDefinitions(File $file)
    {
        $resolvedDefinitions = new ArrayCollection();

        if ($file->isFile() && $file->isReadable()) {
            $loader = $this->getLoaderFactory()->createLoader(
                $this->getFormat($file),
                $file->getPath()
            );

            $resolvedDefinitions = $loader->load($file->getBasename());
        }

        //Overrides existing with new ones always.
        foreach ($this->routeDefinitions as $routeDefinition) {
            $resolvedDefinitions->set($routeDefinition->getId(), $routeDefinition);
        }

        return $resolvedDefinitions;
    }
}
