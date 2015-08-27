<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateRoutingCommand;
use Tdn\ForgeBundle\Generator\RoutingGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;

/**
 * Class GenerateRoutingCommandTest
 * @package Tdn\ForgeBundle\Tests\Command
 */
class GenerateRoutingCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @param bool $overWrite
     * @param string $routingFile
     * @param string $routePrefix
     * @param string $format
     * @param string $entity
     * @param File[] $files
     *
     * @dataProvider optionsProvider
     */
    public function testExecute($overWrite, $routingFile, $routePrefix, $format, $entity, array $files)
    {
        $options = [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => $overWrite,
            '--target-directory' => $this->getOutDir(),
            '--format'           => $format,
            '--prefix'           => $routePrefix,
            '--entity'           => $entity,
            'routing-file'       => $routingFile
        ];

        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($options);
        foreach ($files as $generatedFile) {
            $this->assertRegExp('#' . $generatedFile->getRealPath() . '#', $tester->getDisplay());
        }
    }

    /**
     * @return GenerateRoutingCommand
     */
    protected function getCommand()
    {
        return new GenerateRoutingCommand();
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [
                false,
                'routing',
                'v1',
                'yaml',
                'FooBarBundle:Foo',
                $this->getProcessedYamlFiles()
            ],
            [
                false,
                'routing',
                'v1',
                'xml',
                'FooBarBundle:Foo',
                $this->getProcessedXmlFiles()
            ]
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedFiles()
    {
        return array_merge($this->getProcessedYamlFiles(), $this->getProcessedXmlFiles());
    }

    /**
     * @return Mockery\MockInterface|RoutingGenerator
     */
    protected function getGenerator(array $processedFiles)
    {
        $generator = Mockery::mock('\Tdn\ForgeBundle\Generator\RoutingGenerator');

        return $this->configureGeneratorMock($generator, $processedFiles);
    }

    /**
     * @return File[]
     */
    protected function getProcessedYamlFiles()
    {
        $routingFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Resources' .
                        DIRECTORY_SEPARATOR . 'config' .
                        DIRECTORY_SEPARATOR . 'routing.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return [
            $routingFileMock->getRealPath() => $routingFileMock
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedXmlFiles()
    {
        $routingFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Resources' .
                        DIRECTORY_SEPARATOR . 'config' .
                        DIRECTORY_SEPARATOR . 'routing.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return [
            $routingFileMock->getRealPath() => $routingFileMock
        ];
    }
}
