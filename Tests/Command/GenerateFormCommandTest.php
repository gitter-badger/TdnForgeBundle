<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateFormCommand;
use Tdn\ForgeBundle\Generator\FormGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;

/**
 * Class GenerateFormCommandTest
 * @package Tdn\ForgeBundle\Tests\Command
 */
class GenerateFormCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @param bool $overWrite
     * @param string $entity
     *
     * @dataProvider getOptions
     */
    public function testExecute($overWrite, $entity)
    {
        $options = [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => $overWrite,
            '--target-directory' => $this->getOutDir(),
            '--entity'           => $entity
        ];

        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($options);

        foreach ($this->getProcessedFiles() as $generatedFile) {
            $this->assertRegExp('#' . $generatedFile->getRealPath() . '#', $tester->getDisplay());
        }
    }

    /**
     * @return GenerateFormCommand
     */
    protected function getCommand()
    {
        return new GenerateFormCommand();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                false,
                'FooBarBundle:Foo'
            ]
        ];
    }

    /**
     * @param File[] $processedFiles
     * @return Mockery\MockInterface|FormGenerator
     */
    protected function getGenerator(array $processedFiles)
    {
        $generator = Mockery::mock('\Tdn\ForgeBundle\Generator\FormGenerator');

        return $this->configureGeneratorMock($generator, $processedFiles);
    }

    /**
     * @return File[]
     */
    protected function getProcessedFiles()
    {
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getRealPath() => $formTypeFileMock,
            $exceptionFileMock->getRealPath() => $exceptionFileMock
        ];
    }

    /**
     * @return File
     */
    protected function getFormTypeMock()
    {
        $formTypeFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' .
                        DIRECTORY_SEPARATOR . 'FooType.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $formTypeFileMock;
    }

    /**
     * @return File
     */
    protected function getExceptionFileMock()
    {
        $exceptionFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $exceptionFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
