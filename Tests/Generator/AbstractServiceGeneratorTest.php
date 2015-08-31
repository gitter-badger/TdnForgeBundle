<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\AbstractServiceGenerator;
use Tdn\ForgeBundle\Generator\ServiceGeneratorInterface;
use \Mockery;
use Tdn\ForgeBundle\Model\FormatInterface;
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
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            true,
            []
        );

        /** @var ServiceGeneratorInterface $generator */
        $this->assertEquals($this->getServiceManager(), $generator->getServiceManager());
    }
}
