<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateManagerCommand;
use Tdn\ForgeBundle\Generator\ManagerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;

/**
 * Class GenerateManagerCommandTest
 * @package Tdn\ForgeBundle\Tests\Command
 */
class GenerateManagerCommandTest extends AbstractGeneratorCommandTest
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
     * @return GenerateManagerCommand
     */
    protected function getCommand()
    {
        return new GenerateManagerCommand();
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
     * @param File[] $processedFiles
     *
     * @return Mockery\MockInterface|ManagerGenerator
     */
    protected function getGenerator(array $processedFiles)
    {
        $generator = Mockery::mock('\Tdn\ForgeBundle\Generator\ManagerGenerator');

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
    protected function getProcessedXmlFiles()
    {
        $abstractManagerFileMock = $this->getAbstractManagerFileMock();
        $managerFileMock = $this->getManagerFileMock();
        $managerInterfaceMock = $this->getMgrInterfaceFileMock();
        $managerServiceFile = $this->getXmlManagerServiceMock();

        return [
            $abstractManagerFileMock->getRealPath() => $abstractManagerFileMock,
            $managerFileMock->getRealPath() => $managerFileMock,
            $managerInterfaceMock->getRealPath() => $managerInterfaceMock,
            $managerServiceFile->getRealPath() => $managerServiceFile
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedYamlFiles()
    {
        $abstractManagerFileMock = $this->getAbstractManagerFileMock();
        $managerFileMock = $this->getManagerFileMock();
        $managerInterfaceMock = $this->getMgrInterfaceFileMock();
        $managerServiceFile = $this->getYamlManagerServiceMock();

        return [
            $abstractManagerFileMock->getRealPath() => $abstractManagerFileMock,
            $managerFileMock->getRealPath() => $managerFileMock,
            $managerInterfaceMock->getRealPath() => $managerInterfaceMock,
            $managerServiceFile->getRealPath() => $managerServiceFile
        ];
    }

    /**
     * @return File
     */
    protected function getAbstractManagerFileMock()
    {
        $abstractManagerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $abstractManagerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'AbstractManager.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $abstractManagerFileMock;
    }

    /**
     * @return File
     */
    protected function getManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getMgrInterfaceFileMock()
    {
        $mgrInterfaceFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrInterfaceFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $mgrInterfaceFileMock;
    }

    /**
     * @return File
     */
    protected function getYamlManagerServiceMock()
    {
        $mgrServiceMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.yaml'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }

    /**
     * @return File
     */
    protected function getXmlManagerServiceMock()
    {
        $mgrServiceMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.xml'
                ]
            )
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
