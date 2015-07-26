<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateHandlerCommand;
use Tdn\ForgeBundle\Generator\HandlerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;

/**
 * Class GenerateHandlerCommandTest
 * @package Tdn\ForgeBundle\Tests\Command
 */
class GenerateHandlerCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @param bool $overWrite
     * @param string $format
     * @param string $entity
     * @param File[] $files
     *
     * @dataProvider optionsProvider
     */
    public function testExecute($overWrite, $format, $entity, array $files)
    {
        $options = [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => $overWrite,
            '--target-directory' => $this->getOutDir(),
            '--format'           => $format,
            '--entity'           => $entity
        ];

        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($options);

        foreach ($files as $generatedFile) {
            $this->assertRegExp('#' . $generatedFile->getRealPath() . '#', $tester->getDisplay());
        }
    }

    /**
     * @return GenerateHandlerCommand
     */
    protected function getCommand()
    {
        return new GenerateHandlerCommand();
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [
                false,
                'xml',
                'FooBarBundle:Foo',
                $this->getProcessedXmlFiles()
            ],
            [
                false,
                'yaml',
                'FooBarBundle:Foo',
                $this->getProcessedYamlFiles()
            ]
        ];
    }

    /**
     * @return Mockery\MockInterface|HandlerGenerator
     */
    protected function getGenerator(array $processedFiles)
    {
        $generator = Mockery::mock('\Tdn\ForgeBundle\Generator\HandlerGenerator');

        return $this->configureGeneratorMock($generator, $processedFiles);
    }

    /**
     * @return File[]
     */
    protected function getProcessedFiles()
    {
        return array_merge($this->getProcessedYamlFiles(), $this->getProcessedXmlFiles());
    }

    /**
     * @return File[]
     */
    protected function getProcessedYamlFiles()
    {
        $handlerFileMock = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getHandlerServiceMock();

        return [
            $handlerFileMock->getRealPath() => $handlerFileMock,
            $handlerServiceMock->getRealPath() => $handlerServiceMock
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedXmlFiles()
    {
        $handlerFileMock = $this->getHandlerFileMock();
        $handlerServiceMock = $this->getXmlHandlerServiceMock();

        return [
            $handlerFileMock->getRealPath() => $handlerFileMock,
            $handlerServiceMock->getRealPath() => $handlerServiceMock
        ];
    }

    /**
     * @return File
     */
    protected function getHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php',
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return File
     */
    protected function getHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.yaml',
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }

    /**
     * @return File
     */
    protected function getXmlHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.xml',
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
