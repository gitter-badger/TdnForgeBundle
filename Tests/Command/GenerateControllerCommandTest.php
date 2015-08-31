<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateControllerCommand;
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

        foreach ($this->getFiles() as $file) {
            $this->assertRegExp($this->getCommandDisplayMessage($file), $tester->getDisplay());
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @
     */
    public function testWontExecute()
    {
        $options = [
            'command' => $this->getCommand()->getName()
        ];

        $tester = new CommandTester($this->getFullCommand());
        $tester->execute($options);
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
                'api'
            ]
        ];
    }
}
