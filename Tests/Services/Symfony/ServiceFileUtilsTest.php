<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\ServiceDefinition;
use Tdn\ForgeBundle\Services\Symfony\ServiceManager;

/**
 * Class ServiceFileUtilsTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony
 */
class ServiceFileUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceFileUtils;

    protected function setUp()
    {
        $this->serviceFileUtils = new ServiceManager();
    }

    public function testSupportedExtensions()
    {
        $this->assertContains(Format::YAML, ServiceManager::getSupportedExtensions());
        $this->assertContains(Format::XML, ServiceManager::getSupportedExtensions());
        $this->assertNotContains(Format::ANNOTATION, ServiceManager::getSupportedExtensions());
    }

    public function testAddParameter()
    {
        $paramK = '%foo%';
        $paramV = 'my_value';
        $this->serviceFileUtils->addParameter($paramK, $paramV);
        $this->assertArrayHasKey($paramK, $this->serviceFileUtils->getParameters());
        $this->assertContains($paramV, $this->serviceFileUtils->getParameters());
    }

    public function testAddServiceDefinition()
    {
        $definition = $this->getServiceDefinition();
        $this->serviceFileUtils->addServiceDefinition($definition);
        $this->assertContains($definition, $this->serviceFileUtils->getServiceDefinitions());
    }

    /**
     * @param string $format
     * @param string $fileName
     *
     * @dataProvider dataProvider
     */
    public function testDump($format, $fileName)
    {
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName);
        $this->serviceFileUtils->addParameter(
            'foo_bar.manager.foo_manager.class',
            'Foo\BarBundle\Entity\Manager\FooManager'
        );


        $this->serviceFileUtils->addServiceDefinition($this->getServiceDefinition());
        $this->assertEquals($format, $this->serviceFileUtils->dump($file));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                self::getStaticData('service', 'service.xml'),
                'tmp-service.xml'
            ],
            [
                self::getStaticData('service', 'service.yaml'),
                'tmp-service.yaml'
            ]
        ];
    }

    /**
     * @return ServiceDefinition
     */
    protected function getServiceDefinition()
    {
        $definition = new Definition('%foo_bar.manager.foo_manager.class%');
        $definition
            ->addArgument(new Reference('doctrine'))
            ->addArgument('Foo\BarBundle\Entity\Foo')
        ;

        return new ServiceDefinition('foo_bar.manager.foo_manager', $definition);
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
