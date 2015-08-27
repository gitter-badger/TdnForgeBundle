<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\ForgeBundle\Generator\ControllerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\Format;

/**
 * Class ControllerGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class ControllerGeneratorTest extends AbstractGeneratorTest
{
    public function testPrefix()
    {
        $generator = $this->getGenerator();
        $generator->setPrefix('api');
        $this->assertEquals('api', $generator->getPrefix());
    }

    public function testSwagger()
    {
        $generator = $this->getGenerator();
        $this->assertFalse($generator->hasSwagger());
        $generator->setSwagger(true);
        $this->assertTrue($generator->hasSwagger());
    }

    public function testGenerateTests()
    {
        $generator = $this->getGenerator();
        $this->assertFalse($generator->supportsTests());
        $generator->setTests(true);
        $this->assertTrue($generator->supportsTests());
    }

    public function optionsProvider()
    {
        return [
            [
                //Basic Controller
                Format::YAML, //Arbitrary non-annotation
                true,
                $this->getBasicFiles(),
                [
                    'prefix' => '',
                    'swagger' => false,
                    'tests' => false,
                    'fixtures' => false
                ]
            ],
            [
                //Opinionated Controller
                Format::YAML, //Arbitrary non-annotation
                true,
                $this->getOpinionatedFiles(),
                [
                    'prefix' => '',
                    'swagger' => true,
                    'tests' => false,
                    'fixtures' => false
                ]
            ],
            [
                //Fully Opinionated Controller
                Format::ANNOTATION,
                true,
                $this->getFullyOpinionatedFiles(),
                [
                    'prefix' => 'v1',
                    'swagger' => true,
                    'tests' => false,
                    'fixtures' => false
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    private function getBasicFiles()
    {
        return [
            $this->getBasicControllerFileMock()->getRealPath() => $this->getBasicControllerFileMock()
        ];
    }

    /**
     * @return array
     */
    private function getOpinionatedFiles()
    {
        return [
            $this->getOpinionatedControllerFileMock()->getRealPath() => $this->getOpinionatedControllerFileMock()
        ];
    }

    /**
     * @return array
     */
    private function getFullyOpinionatedFiles()
    {
        return [
            $this->getFullyOpinionatedControllerFileMock()->getRealPath() =>
                $this->getFullyOpinionatedControllerFileMock()
        ];
    }

    /**
     * @param array $options
     *
     * @return ControllerGenerator
     */
    protected function getGenerator(array $options = [])
    {
        $generator = new ControllerGenerator(
            $this->getMetadata(),
            $this->getBundle(),
            $this->getTemplateStrategy(),
            Format::YAML,
            self::getOutDir(),
            false,
            $options,
            false, //not forge
            true   //ignore optional deps checks.
        );

        return $generator;
    }

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        $handlerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
            self::getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($handlerFile)
        ]);
    }

    /**
     * @return File
     */
    protected function getBasicControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('controller', 'BasicController.phps'),
                    'getFilename'  => 'FooController',
                    'getExtension' => 'php',
                    'getPath'      => self::getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getRealPath'  => self::getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }

    /**
     * @return File
     */
    protected function getOpinionatedControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('controller', 'OpinionatedController.phps'),
                    'getFilename'  => 'FooController',
                    'getExtension' => 'php',
                    'getPath'      => self::getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getRealPath'  => self::getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }

    /**
     * @return File
     */
    protected function getFullyOpinionatedControllerFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'  => self::getStaticData('controller', 'FullyOpinionatedController.phps'),
                    'getFilename'  => 'FooController',
                    'getExtension' => 'php',
                    'getPath'      => self::getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
                    'getRealPath'  => self::getOutDir() .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooController.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }

//    /**
//     * @return File
//     */
//    protected function getAbstractControllerFileMock()
//    {
//        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
//        $controllerFileMock
//            ->shouldDeferMissing()
//            ->shouldReceive(
//                [
//                    'getContent'  => ControllerData::ABSTRACT_CONTROLLER_TEST,
//                    'getFilename'  => 'AbstractControllerTest',
//                    'getExtension' => 'php',
//                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
//                    'getRealPath'  => $this->getOutDir() .
//                        DIRECTORY_SEPARATOR . 'Tests' .
//                        DIRECTORY_SEPARATOR . 'Controller' .
//                        DIRECTORY_SEPARATOR . 'AbstractControllerTest.php'
//                ]
//            )
//            ->zeroOrMoreTimes()
//        ;
//
//        return $controllerFileMock;
//    }
//
//    /**
//     * @return File
//     */
//    protected function getControllerTestFileMock()
//    {
//        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
//        $controllerFileMock
//            ->shouldDeferMissing()
//            ->shouldReceive(
//                [
//                    'getContent'  => ControllerData::CONTROLLER_TEST,
//                    'getFilename'  => 'FooControllerTest',
//                    'getExtension' => 'php',
//                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Controller',
//                    'getRealPath'  => $this->getOutDir() .
//                        DIRECTORY_SEPARATOR . 'Tests' .
//                        DIRECTORY_SEPARATOR . 'Controller' .
//                        DIRECTORY_SEPARATOR . 'FooControllerTest.php'
//                ]
//            )
//            ->zeroOrMoreTimes()
//        ;
//
//        return $controllerFileMock;
//    }
}
