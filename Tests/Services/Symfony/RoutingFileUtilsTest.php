<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony;

use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Services\Symfony\RoutingManager;

/**
 * Class RoutingFileUtilsTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony
 */
class RoutingFileUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoutingManager
     */
    protected $routingFileUtils;

    protected function setUp()
    {
        $this->routingFileUtils = new RoutingManager();
    }

    public function testSupportedExtensions()
    {
        $this->assertContains(Format::YAML, RoutingManager::getSupportedExtensions());
        $this->assertContains(Format::XML, RoutingManager::getSupportedExtensions());
        $this->assertNotContains(Format::ANNOTATION, RoutingManager::getSupportedExtensions());
    }

    public function testAddDefinition()
    {
        $routeDefinition = $this->getRouteDefinition();
        $this->assertEmpty($this->routingFileUtils->getRouteDefinitions());
        $this->routingFileUtils->addRouteDefinition($routeDefinition);
        $this->assertContains($routeDefinition, $this->routingFileUtils->getRouteDefinitions());
    }

    /**
     * @param string $format
     * @param string $fileName
     *
     * @dataProvider dataProvider
     */
    public function testDump($format, $fileName)
    {
        $routeDefinition = $this->getRouteDefinition();
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName);
        $this->routingFileUtils->addRouteDefinition($routeDefinition);
        $this->assertEquals($format, $this->routingFileUtils->dump($file));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                self::getStaticData('routing', 'routing.xml'),
                'tmp-routes.xml'
            ],
            [
                self::getStaticData('routing', 'routing.yaml'),
                'tmp-routes.yaml'
            ]
        ];
    }

    /**
     * @return RouteDefinition
     */
    private function getRouteDefinition()
    {
        return new RouteDefinition(
            'api_foo_v1',
            '@FooBarBundle/Controller/FooController.php',
            'v1',
            'rest'
        );
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
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'static' .
            DIRECTORY_SEPARATOR . $directory
        );

        $path = $path . DIRECTORY_SEPARATOR . $file;

        return file_get_contents($path);
    }
}
