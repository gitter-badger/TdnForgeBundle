<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\FormatInterface;
use Tdn\ForgeBundle\Model\ServiceDefinition;
use Tdn\ForgeBundle\Services\Symfony\ServiceManager;

/**
 * Class ServiceManagerTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony
 */
class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected function setUp()
    {
        $this->serviceManager = new ServiceManager();
    }

    public function testSupportedFormats()
    {
        $this->assertContains(FormatInterface::YAML, ServiceManager::getSupportedExtensions());
        $this->assertContains(FormatInterface::XML, ServiceManager::getSupportedExtensions());
        $this->assertNotContains(FormatInterface::ANNOTATION, ServiceManager::getSupportedExtensions());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Invalid format. Expected one of (.*), got (.*)./
     */
    public function testNotSupportedFormat()
    {
        $this->serviceManager->dump(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foo.zip');
    }

    public function testAddParameter()
    {
        $paramK = '%foo%';
        $paramV = 'my_value';
        $this->serviceManager->addParameter($paramK, $paramV);
        $this->assertArrayHasKey($paramK, $this->serviceManager->getParameters());
        $this->assertContains($paramV, $this->serviceManager->getParameters());
    }

    public function testAddServiceDefinition()
    {
        $definition = $this->getServiceDefinition();
        $this->serviceManager->addServiceDefinition($definition);
        $this->assertContains($definition, $this->serviceManager->getServiceDefinitions());
    }

    public function testDumpExisting()
    {
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foo.yaml');
        $file->openFile('w')->fwrite('');

        $this->serviceManager->addParameter(
            'foo_bar.manager.foo_manager.class',
            'Foo\BarBundle\Entity\Manager\FooManager'
        );

        $this->serviceManager->addServiceDefinition($this->getServiceDefinition());
        $this->assertEquals(
            self::getStaticData('service', 'service.yaml'),
            $this->serviceManager->dump($file->getRealPath())
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
        $this->serviceManager->addParameter(
            'foo_bar.manager.foo_manager.class',
            'Foo\BarBundle\Entity\Manager\FooManager'
        );


        $this->serviceManager->addServiceDefinition($this->getServiceDefinition());
        $this->assertEquals(
            $format,
            $this->serviceManager->dump(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName)
        );
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
