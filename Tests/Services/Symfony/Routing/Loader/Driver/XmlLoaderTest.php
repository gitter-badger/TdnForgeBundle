<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Loader\Driver;

use Symfony\Component\Config\FileLocator;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\XmlLoader;
use Tdn\ForgeBundle\Tests\Services\Symfony\Routing\AbstractRoutingTest;

class XmlLoaderTest extends AbstractRoutingTest
{
    public function testLoad()
    {
        $xmlResource = new File(
            realpath(
                __DIR__
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'Fixtures'
                . DIRECTORY_SEPARATOR . 'static'
                . DIRECTORY_SEPARATOR . 'routing'
            )
            . DIRECTORY_SEPARATOR
            . 'routing.xml'
        );

        $loader = new XmlLoader(new FileLocator($xmlResource->getPath()));
        $this->assertEquals($this->getRouteCollection(), $loader->load($xmlResource->getBasename()));
    }

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\InvalidXmlException
     * @expectedExceptionMessageRegExp /Error\(s\) parsing file: (.*)./
     */
    public function testNoLoad()
    {
        $xmlResource = new File(
            realpath(
                __DIR__
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'Fixtures'
                . DIRECTORY_SEPARATOR . 'static'
                . DIRECTORY_SEPARATOR . 'routing'
            )
            . DIRECTORY_SEPARATOR
            . 'bad-routing.xml'
        );

        $loader = new XmlLoader(new FileLocator($xmlResource->getPath()));
        $loader->load($xmlResource->getBasename());
    }
}
