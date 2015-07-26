<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Container;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Command\AbstractGeneratorCommand;
use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use \Mockery as Mockery;

abstract class AbstractGeneratorCommandTest extends AbstractCommandTest
{
    /**
     * Create generator object.
     * @param File[] $processedFiles
     *
     * @return GeneratorInterface
     */
    abstract protected function getGenerator(array $processedFiles);

    /**
     * @return File[]
     */
    abstract protected function getProcessedFiles();

    /**
     * @return void
     */
    public function testGenerator()
    {
        $command = $this->getFullCommand();
        $this->assertEquals(
            $this->getGenerator($this->getProcessedFiles()),
            $command->getGenerator()
        );
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
        $command->setGenerator($this->getGenerator($this->getProcessedFiles()));

        return $command;
    }

    /**
     * @return GeneratorFactoryInterface
     */
    protected function getGeneratorFactory()
    {
        $generatorFactory = Mockery::mock('Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface');
        $generatorFactory
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'create' => $this->getGenerator($this->getProcessedFiles())
                ]
            )
            ->zeroOrMoreTimes()
        ;
    }

    /**
     * @param Mockery\MockInterface $generator
     * @param array $processedFiles
     *
     * @return GeneratorInterface
     */
    protected function configureGeneratorMock(Mockery\MockInterface $generator, array $processedFiles)
    {
        $generator
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'isValid' => true,
                    'configure'  => $generator,
                    'generate' => new ArrayCollection($processedFiles),
                    'getFiles' => new ArrayCollection($processedFiles),
                    'getMessages' => new ArrayCollection()
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $generator;
    }
}
