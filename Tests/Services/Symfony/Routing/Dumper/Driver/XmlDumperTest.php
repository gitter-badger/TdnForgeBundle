<?php

namespace Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Dumper\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver\XmlDumper;
use Tdn\ForgeBundle\Tests\Services\Symfony\Routing\AbstractRoutingTest;

/**
 * Class XmlDumperTest
 * @package Tdn\ForgeBundle\Tests\Services\Symfony\Routing\Dumper\Driver
 */
class XmlDumperTest extends AbstractRoutingTest
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
    }

    public function testExport()
    {
        $dumper = new XmlDumper($this->routeCollection);
        $this->assertEquals(self::getStaticData('routing', 'routing.xml'), $dumper->dump());
    }
}
