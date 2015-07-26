<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\AbstractServiceGenerator;
use Tdn\ForgeBundle\Generator\ServiceGeneratorInterface;
use \Mockery;
use Tdn\ForgeBundle\Tests\Traits\ServiceManagerMock;

/**
 * Class AbstractServiceGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
abstract class AbstractServiceGeneratorTest extends AbstractGeneratorTest
{
    use ServiceManagerMock;

    public function testServiceUtils()
    {
        /** @var ServiceGeneratorInterface $generator */
        $generator = $this->getGenerator();
        $this->assertNotNull($generator->getServiceManager());
        $generator->setServiceManager($this->getServiceManager());
        $this->assertEquals($this->getServiceManager(), $generator->getServiceManager());
    }
}
