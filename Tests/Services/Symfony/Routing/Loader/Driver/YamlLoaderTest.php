<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Loader\Driver;

use Symfony\Component\Config\FileLocator;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\YamlLoader;
use Tdn\ForgeBundle\Tests\Services\Symfony\Routing\AbstractRoutingTest;

class YamlLoaderTest extends AbstractRoutingTest
{
    public function testLoad()
    {
        $yaml = new File(
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
            . 'routing.yaml'
        );

        $loader = new YamlLoader(new FileLocator($yaml->getPath()));
        $this->assertEquals($this->getRouteCollection(), $loader->load($yaml->getBasename()));
    }
}
