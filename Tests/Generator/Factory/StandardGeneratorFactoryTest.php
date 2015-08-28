<?php

namespace Tdn\ForgeBundle\Tests\Generator\Factory;

use Symfony\Component\Filesystem\Filesystem;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Generator\ServiceGeneratorInterface;
use Tdn\ForgeBundle\Generator\Factory\StandardGeneratorFactory;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;
use Tdn\ForgeBundle\Tests\Traits\ServiceManagerMock;
use Tdn\ForgeBundle\Tests\Traits\TwigStrategyMock;
use \Mockery;

/**
 * Class StandardGeneratorFactoryTest
 * @package Tdn\ForgeBundle\Tests\Generator\Factory
 */
class StandardGeneratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    use BundleMock;
    use MetadataMock;
    use TwigStrategyMock;
    use ServiceManagerMock;

    /**
     * @var StandardGeneratorFactory
     */
    protected $standardGeneratorFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->bundle = $this->createBundle();
        $this->outDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'forge-bundle';
        $this->standardGeneratorFactory = new StandardGeneratorFactory(
            $this->getTemplateStrategy(),
            $this->getServiceManager()
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        $this->standardGeneratorFactory = new StandardGeneratorFactory(
            $this->templateStrategy,
            $this->getServiceManager()
        );

        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Invalid type \'.*\'. Supported types are: .*$/
     */
    public function testBadGeneratorInstance()
    {
        $this->standardGeneratorFactory->create(
            'BadGeneratorType',
            $this->getMetadata(),
            $this->getBundle(),
            Format::YAML,
            '',
            false,
            []
        );
    }

    public function testControllerGeneratorInstance()
    {
        $this->assertContains(
            GeneratorFactoryInterface::TYPE_CONTROLLER_GENERATOR,
            StandardGeneratorFactory::getSupportedTypes()
        );

        $this->assertInstanceOf(
            '\Tdn\ForgeBundle\Generator\ControllerGenerator',
            $this->standardGeneratorFactory->create(
                GeneratorFactoryInterface::TYPE_CONTROLLER_GENERATOR,
                $this->getMetadata(),
                $this->getBundle(),
                Format::YAML,
                $this->outDir,
                false,
                []
            )
        );
    }

    public function testFormGeneratorInstance()
    {
        $this->assertContains(
            GeneratorFactoryInterface::TYPE_FORM_GENERATOR,
            StandardGeneratorFactory::getSupportedTypes()
        );

        $this->assertInstanceOf(
            '\Tdn\ForgeBundle\Generator\FormGenerator',
            $this->standardGeneratorFactory->create(
                GeneratorFactoryInterface::TYPE_FORM_GENERATOR,
                $this->getMetadata(),
                $this->getBundle(),
                Format::YAML,
                '',
                false,
                []
            )
        );
    }

    public function testHandlerGeneratorInstance()
    {
        $this->assertContains(
            GeneratorFactoryInterface::TYPE_HANDLER_GENERATOR,
            StandardGeneratorFactory::getSupportedTypes()
        );

        /** @var ServiceGeneratorInterface $generator */
        $generator = $this->standardGeneratorFactory->create(
            GeneratorFactoryInterface::TYPE_HANDLER_GENERATOR,
            $this->getMetadata(),
            $this->getBundle(),
            Format::YAML,
            '',
            false,
            []
        );

        $this->assertInstanceOf('\Tdn\ForgeBundle\Generator\HandlerGenerator', $generator);
        $this->assertService($generator);
    }

    public function testManagerGeneratorInstance()
    {
        $this->assertContains(
            GeneratorFactoryInterface::TYPE_MANAGER_GENERATOR,
            StandardGeneratorFactory::getSupportedTypes()
        );

        /** @var ServiceGeneratorInterface $generator */
        $generator = $this->standardGeneratorFactory->create(
            GeneratorFactoryInterface::TYPE_MANAGER_GENERATOR,
            $this->getMetadata(),
            $this->getBundle(),
            Format::YAML,
            '',
            false,
            []
        );

        $this->assertInstanceOf('\Tdn\ForgeBundle\Generator\ManagerGenerator', $generator);
        $this->assertService($generator);
    }

    public function testRoutingGeneratorInstance()
    {
        $this->assertContains(
            GeneratorFactoryInterface::TYPE_ROUTING_GENERATOR,
            StandardGeneratorFactory::getSupportedTypes()
        );

        $this->assertInstanceOf(
            '\Tdn\ForgeBundle\Generator\RoutingGenerator',
            $this->standardGeneratorFactory->create(
                GeneratorFactoryInterface::TYPE_ROUTING_GENERATOR,
                $this->getMetadata(),
                $this->getBundle(),
                Format::YAML,
                '',
                false,
                []
            )
        );
    }

    /**
     * @param ServiceGeneratorInterface $generator
     */
    private function assertService(ServiceGeneratorInterface $generator)
    {
        $this->assertInstanceOf('\Tdn\ForgeBundle\Services\Symfony\ServiceManager', $generator->getServiceManager());
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }
}
