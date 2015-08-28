<?php

namespace Tdn\ForgeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Tdn\ForgeBundle\Command\GenerateFormCommand;
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

        foreach ($this->getFiles() as $file) {
            $this->assertRegExp(
                $this->getCommandDisplayMessage($file),
                $tester->getDisplay()
            );
        }
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return 'forge:generate:form';
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
}
