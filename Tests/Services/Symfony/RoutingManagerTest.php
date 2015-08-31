<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony;

use Tdn\ForgeBundle\Model\FormatInterface;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Services\Symfony\RoutingManager;

/**
 * Class RoutingManagerTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony
 */
class RoutingManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoutingManager
     */
    protected $routingManager;

    protected function setUp()
    {
        $this->routingManager = new RoutingManager();
    }

    public function testSupportedFormat()
    {
        $this->assertContains(FormatInterface::YAML, RoutingManager::getSupportedExtensions());
        $this->assertContains(FormatInterface::XML, RoutingManager::getSupportedExtensions());
        $this->assertNotContains(FormatInterface::ANNOTATION, RoutingManager::getSupportedExtensions());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Invalid format. Expected one of (.*), got (.*)./
     */
    public function testNotSupportedFormat()
    {
        $this->routingManager->dump(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foo.zip');
    }

    public function testAddDefinition()
    {
        $routeDefinition = $this->getRouteDefinition();
        $this->assertEmpty($this->routingManager->getRouteDefinitions());
        $this->routingManager->addRouteDefinition($routeDefinition);
        $this->assertContains($routeDefinition, $this->routingManager->getRouteDefinitions());
    }

    public function testDumpExisting()
    {
        $routeDefinition = $this->getRouteDefinition();
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foo.yaml');
        $file->openFile('w')->fwrite('');

        $this->routingManager->addRouteDefinition($routeDefinition);
        $this->assertEquals(
            self::getStaticData('routing', 'routing.yaml'),
            $this->routingManager->dump($file->getRealPath())
        );
        unlink($file->getRealPath());
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
        $this->routingManager->addRouteDefinition($routeDefinition);
        $this->assertEquals(
            $format,
            $this->routingManager->dump(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName)
        );
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
            'foobar_api_foo',
            '@FooBarBundle/Controller/FooController.php',
            'api',
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
