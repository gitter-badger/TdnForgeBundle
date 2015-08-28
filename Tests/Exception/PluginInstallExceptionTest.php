<?php

namespace Tdn\ForgeBundle\Tests\Exception;

use Tdn\ForgeBundle\Exception\PluginInstallException;

/**
 * Class PluginInstallExceptionTest
 * @package Tdn\ForgeBundle\Tests\Exception
 */
class PluginInstallExceptionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException        \Tdn\ForgeBundle\Exception\PluginInstallException
     * @expectedExceptionMessage Error installing plugin: Foo
     */
    public function testExceptionMessage()
    {
        throw new PluginInstallException('Foo');
    }
}
