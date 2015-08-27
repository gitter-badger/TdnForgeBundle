<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\FormatterHelper;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Tdn\ForgeBundle\Command\AbstractGeneratorCommand;
use Tdn\ForgeBundle\Generator\Factory\StandardGeneratorFactory;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;
use Tdn\ForgeBundle\Services\Symfony\RoutingManager;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;
use Tdn\ForgeBundle\Tests\Traits\ServiceManagerMock;
use Tdn\ForgeBundle\Tests\Traits\TwigStrategyMock;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use \Mockery as Mockery;

/**
 * Class AbstractCommandTest
 * @package Tdn\ForgeBundle\Tests\Command
 */
abstract class AbstractCommandTest extends GenerateCommandTest
{
    use MetadataMock;
    use BundleMock;
    use TwigStrategyMock;
    use ServiceManagerMock;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    protected $outDir;

    /**
     * @return AbstractGeneratorCommand
     */
    abstract protected function getCommand();

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->outDir       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-forge';
        $this->filesystem   = new Filesystem();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }

    /**
     * @return string
     */
    protected function getOutDir()
    {
        return $this->outDir;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = parent::getContainer();

        $container->set('doctrine', $this->getDoctrine());
        $container->set('tdn_forge.doctrine.entity.helper', $this->getEntityHelper());
        $container->set('tdn_forge.template.strategy.default', $this->getTemplateStrategy());
        $container->set('tdn_forge.symfony.service.manager', $this->getServiceManager());
        $container->set('tdn_forge.symfony.routing.manager', $this->getRoutingManager());
        $container->set('tdn_forge.generator.factory.standard_generator_factory', $this->getGeneratorFactory());

        return $container;
    }

    /**
     * @param  string    $input
     * @return HelperSet
     */
    protected function getHelperSet($input)
    {
        $formatter = new FormatterHelper();
        $question = new QuestionHelper();
        $question->setInputStream($this->getInputStream($input));

        return new HelperSet([$formatter, $question]);
    }

    /**
     * @return Application
     */
    protected function getApplication()
    {
        $application = new Application();
        $application->add($this->getCommand());

        return $application;
    }

    /**
     * @return GeneratorFactoryInterface
     */
    private function getGeneratorFactory()
    {
        $generatorFactory = Mockery::mock(
            new StandardGeneratorFactory($this->getTemplateStrategy(), $this->getServiceManager())
        );

        $generatorFactory->shouldDeferMissing();

        return $generatorFactory;
    }

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine()
    {
        $registry = Mockery::mock('\Symfony\Bridge\Doctrine\ManagerRegistry');
        $registry
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getAliasNamespace' => 'Foo\BarBundle\Entity',
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $registry;
    }

    /**
     * @return EntityHelper
     */
    private function getEntityHelper()
    {
        $entityUtils = Mockery::mock('\Tdn\ForgeBundle\Services\Doctrine\EntityHelper');
        $entityUtils
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getMetadata' => $this->getMetadata()
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $entityUtils;
    }

    /**
     * @return RoutingManager
     */
    private function getRoutingManager()
    {
        $serviceFileUtils = Mockery::mock('\Tdn\ForgeBundle\Services\Symfony\RoutingManager');
        $serviceFileUtils->shouldDeferMissing();

        return $serviceFileUtils;
    }
}
