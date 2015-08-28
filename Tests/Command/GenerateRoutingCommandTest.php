<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateRoutingCommand;
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
     *
     * @dataProvider optionsProvider
     */
    public function testExecute($overWrite, $routingFile, $routePrefix, $format, $entity)
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
        foreach ($this->getFiles() as $file) {
            $this->assertRegExp($this->getCommandDisplayMessage($file), $tester->getDisplay());
        }
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return 'forge:generate:routing';
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
                'api',
                'yaml',
                'FooBarBundle:Foo'
            ],
            [
                false,
                'routing',
                'api',
                'xml',
                'FooBarBundle:Foo'
            ]
        ];
    }
}
