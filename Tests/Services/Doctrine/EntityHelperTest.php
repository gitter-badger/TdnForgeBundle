<?php

namespace Tdn\ForgeBundle\Tests\Services\Doctrine;

use \Mockery;
use Doctrine\Common\Persistence\ManagerRegistry;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;

/**
 * Class EntityHelperTest
 * @package Tdn\ForgeBundle\Tests\Services\Doctrine
 */
class EntityHelperTest extends \PHPUnit_Framework_TestCase
{
    use BundleMock;
    use MetadataMock;

    /**
     * @var EntityHelper
     */
    protected $entityUtils;

    protected function setUp()
    {
        $this->entityUtils = $this->getEntityHelper();
    }

    public function testGetClassesInDirectory()
    {
        $fixturesDir = new \SplFileInfo(
            realpath(
                __DIR__
                . DIRECTORY_SEPARATOR . ".."
                . DIRECTORY_SEPARATOR . ".."
                . DIRECTORY_SEPARATOR . 'Fixtures'
                . DIRECTORY_SEPARATOR . 'dummy-entities'
            )
        );

        $fixtureEntities = [
            'FooBundle:Foo',
            'FooBundle:Bar',
            'FooBundle:Baz',
        ];

        $actualFixtures = $this->entityUtils
            ->getClassesInDirectory($fixturesDir->getRealPath(), 'FooBundle')
            ->toArray()
        ;

        sort($fixtureEntities);
        sort($actualFixtures);
        $this->assertEquals(
            $fixtureEntities,
            $actualFixtures
        );
    }

    public function testMetadata()
    {
        $this->assertInstanceOf(
            '\Doctrine\ORM\Mapping\ClassMetadata',
            $this->entityUtils->getMetadata($this->getBundle(), 'Foo')
        );
    }

    /**
     * @return EntityHelper
     */
    private function getEntityHelper()
    {
        $entityUtils = Mockery::mock(new EntityHelper($this->getDoctrine()));
        $entityUtils
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getMetadata' => $this->getMetadata()
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $entityUtils;
    }

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine()
    {
        return Mockery::mock('\Doctrine\Common\Persistence\ManagerRegistry');
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
