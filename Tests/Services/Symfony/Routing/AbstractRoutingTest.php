<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class AbstractRoutingTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony\Routing
 */
abstract class AbstractRoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ArrayCollection
     */
    protected function getRouteCollection()
    {
        $collection = new ArrayCollection();
        $routeDefinition = new RouteDefinition(
            'foobar_api_foo',
            '@FooBarBundle/Controller/FooController.php',
            'api',
            'rest'
        );

        $collection->set($routeDefinition->getId(), $routeDefinition);
        return $collection;
    }

    /**
     * @param string $directory
     * @param string $file

     * @return string
     */
    protected static function getStaticData($directory, $file)
    {
        $path = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'static' .
            DIRECTORY_SEPARATOR . $directory
        );

        $path = $path . DIRECTORY_SEPARATOR . $file;

        return file_get_contents($path);
    }
}
