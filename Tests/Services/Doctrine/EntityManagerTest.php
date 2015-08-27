<?php

namespace Tdn\ForgeBundle\Tests\Services\Doctrine;

use \Mockery;
use Doctrine\Common\Persistence\ManagerRegistry;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    use BundleMock;
    use MetadataMock;

    /**
     * @var EntityHelper
     */
    protected $entityUtils;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->bundle = $this->createBundle();
        $this->metadata = $this->createMetadata();

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        $this->entityUtils = $this->getEntityUtils();
        $this->bundle      = $this->createBundle();
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
            $this->entityUtils->getMetadata($this->bundle, 'Foo')
        );
    }

    /**
     * @return EntityHelper
     */
    private function getEntityUtils()
    {
        $entityUtils = Mockery::mock(new EntityHelper($this->getDoctrine()));
        $entityUtils
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getMetadata' => $this->metadata
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
