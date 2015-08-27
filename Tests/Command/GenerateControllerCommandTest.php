<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateControllerCommand;
use Tdn\ForgeBundle\Generator\ControllerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;

/**
 * Class GenerateControllerCommandTest
 * @package Tdn\ForgeBundle\Test\Command
 */
class GenerateControllerCommandTest extends AbstractGeneratorCommandTest
{
    /**
     * @param bool $overWrite
     * @param bool $swagger
     * @param string $entity
     * @param string $prefix
     *
     * @dataProvider optionsProvider
     */
    public function testExecute($overWrite, $swagger, $entity, $prefix)
    {
        $options = [
            'command'            => $this->getCommand()->getName(),
            '--overwrite'        => $overWrite,
            '--target-directory' => $this->getOutDir(),
            '--with-swagger'     => $swagger,
            '--entity'           => $entity,
            '--prefix'           => $prefix
        ];

        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($options);

        foreach ($this->getProcessedFiles() as $generatedFile) {
            $this->assertRegExp('#' . $generatedFile->getRealPath() . '#', $tester->getDisplay());
        }
    }

    /**
     * @return GenerateControllerCommand
     */
    protected function getCommand()
    {
        return new GenerateControllerCommand();
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return 'forge:generate:controller';
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [
                false,
                true,
                'FooBarBundle:Foo',
                'v1'
            ]
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedFiles()
    {
        $controllerFileMock = $this->getControllerFileMock();

        return [
            $controllerFileMock->getRealPath() => $controllerFileMock
        ];
    }

    /**
     * @param File[] $processedFiles
     *
     * @return ControllerGenerator
     */
    protected function getGenerator(array $processedFiles)
    {
        $generator = Mockery::mock('\Tdn\ForgeBundle\Generator\ControllerGenerator');

        return $this->configureGeneratorMock($generator, $processedFiles);
    }

    /**
     * @return File
     */
    public function getControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getRealPath'  =>  DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }
}
