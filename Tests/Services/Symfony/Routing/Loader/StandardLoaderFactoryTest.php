<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Loader;

use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Services\Symfony\Routing\Loader\StandardLoaderFactory;

/**
 * Class StandardLoaderFactoryTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Loader
 */
class StandardLoaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StandardLoaderFactory
     */
    protected $standardLoaderFactory;

    protected function setUp()
    {
        $this->standardLoaderFactory = new StandardLoaderFactory();
    }

    public function testYamlLoader()
    {
        $this->assertInstanceOf(
            '\Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\YamlLoader',
            $this->standardLoaderFactory->createLoader(Format::YAML, sys_get_temp_dir() . 'foo.yml')
        );
    }

    public function testXmlLoader()
    {
        $this->assertInstanceOf(
            '\Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\XmlLoader',
            $this->standardLoaderFactory->createLoader(Format::XML, sys_get_temp_dir() . 'foo.xml')
        );
    }

    /**
     * @expectedException \Tuck\ConverterBundle\Exception\UnknownFormatException
     * @expectedExceptionMessageRegExp /No adapter found for format \'(.*)\'/
     */
    public function testInvalidLoader()
    {
        $this->standardLoaderFactory->createLoader('no-type', sys_get_temp_dir() . 'foo.zip');
    }
}
