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
    private $entityUtils;

    /**
     * @var \SplFileInfo
     */
    private $fixturesDir;

    protected function setUp()
    {
        $this->entityUtils = new EntityHelper($this->getDoctrine());
        $this->fixturesDir = new \SplFileInfo(
            realpath(
                __DIR__
                . DIRECTORY_SEPARATOR . ".."
                . DIRECTORY_SEPARATOR . ".."
                . DIRECTORY_SEPARATOR . 'Fixtures'
                . DIRECTORY_SEPARATOR . 'dummy-entities'
            )
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $fixtureEntities
     * @param array $excluding
     */
    public function testGetClassesInDirectory(array $fixtureEntities, array $excluding = [])
    {
        $actualFixtures = $this->entityUtils
            ->getClassesInDirectory($this->fixturesDir->getRealPath(), 'FooBundle', $excluding)
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

    public function dataProvider()
    {
        return [
            [['FooBundle:Foo', 'FooBundle:Bar', 'FooBundle:Baz']],
            [['FooBundle:Foo', 'FooBundle:Baz'], ['Bar']]
        ];
    }

    public function testDoctrine()
    {
        $this->assertEquals($this->getDoctrine(), $this->entityUtils->getManagerRegistry());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Could not find metadata for class (.*). Error: (.*)/
     */
    public function testBadMetadata()
    {
        $entityHelper = new EntityHelper($this->getDoctrine(true));
        $entityHelper->getMetadata($this->getBundle(), 'Foo');
    }

    /**
     * @param bool $badMetadata
     *
     * @return ManagerRegistry
     */
    private function getDoctrine($badMetadata = false)
    {
        $metadataFactory = Mockery::mock('\Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        if ($badMetadata) {
            $metadataFactory
                ->shouldIgnoreMissing()
                ->shouldReceive('getMetadataFor')
                ->andThrow(new \Exception('Foo'))
            ;
        } else {
            $metadataFactory
                ->shouldIgnoreMissing()
                ->shouldReceive([
                    'getMetadataFor' => $this->getMetadata()
                ])
                ->withAnyArgs();
        }

        $om = Mockery::mock('\Doctrine\Common\Persistence\ObjectManager');
        $om
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'getMetadataFactory' => $metadataFactory
            ])
        ;

        $doctrine = Mockery::mock('\Doctrine\Common\Persistence\ManagerRegistry');
        $doctrine
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'getManagerForClass' => $om
            ])
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $doctrine;
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
