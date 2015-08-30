<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateManagerCommand;
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
     *
     * @dataProvider optionsProvider
     */
    public function testExecute($overWrite, $format, $entity)
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

        foreach ($this->getFiles() as $file) {
            $this->assertRegExp($this->getCommandDisplayMessage($file), $tester->getDisplay());
        }
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return 'forge:generate:manager';
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
                'FooBarBundle:Foo'
            ],
            [
                false,
                'yaml',
                'FooBarBundle:Foo'
            ]
        ];
    }
}
