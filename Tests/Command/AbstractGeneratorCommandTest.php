<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Application;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Command\AbstractGeneratorCommand;
use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use Tdn\ForgeBundle\Writer\Strategy\WriterStrategyInterface;
use \Mockery as Mockery;

abstract class AbstractGeneratorCommandTest extends GenerateCommandTest
{
    use MetadataMock;
    use BundleMock;

    /**
     * @return AbstractGeneratorCommand
     */
    abstract protected function getCommand();
    /**
     * @return string
     */
    abstract protected function getCommandName();

    public function testCommandName()
    {
        $command = $this->getFullCommand();
        $this->assertEquals($this->getCommandName(), $command->getName());
    }

    /**
     * @return AbstractGeneratorCommand
     */
    protected function getFullCommand()
    {
        /** @var AbstractGeneratorCommand $command */
        $command = $this->getApplication()->find($this->getCommand()->getName());
        $command->setHelperSet($this->getHelperSet('y'));
        $command->setContainer($this->getContainer());

        return $command;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        $container = parent::getContainer();

        $container->set('tdn_forge.doctrine.entity.helper', $this->getEntityHelper());
        $container->set('tdn_forge.generator.factory.standard_generator_factory', $this->getGeneratorFactory());
        $container->set('tdn_forge.writer.strategy.default', $this->getWriterStrategy());

        return $container;
    }

    /**
     * @return Application
     */
    private function getApplication()
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
        $generatorFactory = Mockery::mock('\Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface');
        $generatorFactory
            ->shouldIgnoreMissing()
            ->shouldReceive(
                [
                    'create' => $this->getGeneratorMock()
                ]
            )
        ;

        return $generatorFactory;
    }

    /**
     * @return GeneratorInterface
     */
    private function getGeneratorMock()
    {
        $generator = Mockery::mock('Tdn\ForgeBundle\Generator\GeneratorInterface');
        $generator
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'getMessages' => new ArrayCollection(),
                'generate'    => $this->getFiles()
            ])
            ->zeroOrMoreTimes()
        ;

        return $generator;
    }

    /**
     * @return WriterStrategyInterface
     */
    private function getWriterStrategy()
    {
        $generator = Mockery::mock('Tdn\ForgeBundle\Writer\Strategy\WriterStrategyInterface');
        $generator
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'writeFile' => null,
            ])
            ->zeroOrMoreTimes()
        ;

        return $generator;
    }

    /**
     * @return EntityHelper
     */
    private function getEntityHelper()
    {
        $entityUtils = Mockery::mock('\Tdn\ForgeBundle\Services\Doctrine\EntityHelper');
        $entityUtils
            ->shouldIgnoreMissing()
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
     * @return ArrayCollection|File[]
     */
    protected function getFiles()
    {
        $files = new ArrayCollection();
        $mock1 = $this->createFileMock('foo', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'foo.php');
        $mock2 = $this->createFileMock('bar', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bar.php');
        $files->set($mock1->getRealPath(), $mock1);
        $files->set($mock2->getRealPath(), $mock2);

        return $files;
    }

    /**
     * @param $name
     * @param $path
     *
     * @return File
     */
    protected function createFileMock($name, $path)
    {
        $fileMock = Mockery::mock('Tdn\ForgeBundle\Model\File');
        $fileMock
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'getFilename' => $name,
                'getRealPath' => $path
            ])
            ->zeroOrMoreTimes()
        ;

        return $fileMock;
    }

    /**
     * @return string
     */
    protected function getOutDir()
    {
        return sys_get_temp_dir();
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function getCommandDisplayMessage(File $file)
    {
        return sprintf(
            '#The new %s file has been created under %s.#',
            $file->getFilename(),
            $file->getRealPath()
        );
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        Mockery::close();
    }
}
