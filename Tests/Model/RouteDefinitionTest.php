<?php

namespace Tdn\ForgeBundle\Tests\Model;

use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class RouteDefinitionTest
 * @package Tdn\ForgeBundle\Tests\Model
 */
class RouteDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $id = 'route_foo';
        $resource = '@FooBundle/Controller/BarController.php';
        $prefix = 'v1';
        $type = 'rest';

        $routeDefinition = new RouteDefinition(
            $id,
            $resource,
            $prefix,
            $type
        );

        $this->assertEquals($id, $routeDefinition->getId());
        $this->assertEquals($resource, $routeDefinition->getResource());
        $this->assertEquals($prefix, $routeDefinition->getPrefix());
        $this->assertEquals($type, $routeDefinition->getType());
    }
}
