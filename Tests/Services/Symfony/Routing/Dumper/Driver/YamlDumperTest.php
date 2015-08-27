<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Dumper\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\YamlDumper;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Tests\Services\Symfony\Routing\AbstractRoutingTest;

/**
 * Class YamlDumperTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Dumper\Driver
 */
class YamlDumperTest extends AbstractRoutingTest
{
    /**
     * @var string
     */
    protected $fixtureDirPath;

    /**
     * @var ArrayCollection|RouteDefinition[]
     */
    private $routeCollection;

    protected function setUp()
    {
        parent::setUp();

        $this->routeCollection = $this->getRouteCollection();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->routeCollection = null;
        $this->fixtureDirPath = null;
    }

    public function testExport()
    {
        $dumper = new YamlDumper($this->routeCollection);
        $this->assertEquals(self::getStaticData('routing', 'routing.yaml'), $dumper->dump());
    }
}
