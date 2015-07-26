<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;
use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class YamlLoader
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver
 */
class YamlLoader extends AbstractLoader implements LoaderInterface
{
    /**
     * @param $resource
     * @return ArrayCollection|RouteDefinition[]
     */
    public function load($resource)
    {
        $path = $this->locator->locate($resource);
        $content = $this->loadFile($path);

        return $this->loadRoutes($content);
    }

    /**
     * @param $path
     * @return array
     */
    protected function loadFile($path)
    {
        $yamlParser = new Yaml();

        return $yamlParser->parse(file_get_contents($path));
    }

    /**
     * @param array $routes
     * @return ArrayCollection
     */
    protected function loadRoutes(array $routes)
    {
        $routeCollection = new ArrayCollection();

        foreach ($routes as $id => $route) {
            $routeDefinition = new RouteDefinition(
                $id,
                $route['resource'],
                $route['prefix'],
                $route['type']
            );

            $routeCollection->set($id, $routeDefinition);
        }

        return $routeCollection;
    }
}
