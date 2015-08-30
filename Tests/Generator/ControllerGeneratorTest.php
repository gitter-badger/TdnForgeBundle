<?php

namespace Tdn\ForgeBundle\Tests\Generator;

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

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\CoreDependencyMissingException
     * @expectedExceptionMessageRegExp /Please ensure the file (.*) exists and is readable./
     */
    public function testDependencyMissing()
    {
        $generator = $this->getGenerator(
            Format::YAML,
            self::getOutDir(),
            false,
            [],
            false
        );

        $generator->generate();
    }

    public function optionsProvider()
    {
        return [
            [
                //Basic Controller
                Format::YAML, //Arbitrary non-annotation
                self::getOutDir(),
                true,
                [
                    'prefix' => '',
                    'swagger' => false,
                    'tests' => false
                ],
                $this->getBasicFiles()
            ],
            [
                //Opinionated Controller
                Format::YAML, //Arbitrary non-annotation
                self::getOutDir(),
                true,
                [
                    'prefix' => '',
                    'swagger' => true,
                    'tests' => false
                ],
                $this->getOpinionatedFiles()
            ],
            [
                //Fully Opinionated Controller
                Format::ANNOTATION,
                self::getOutDir(),
                true,
                [
                    'prefix' => 'api',
                    'swagger' => true,
                    'tests' => false
                ],
                $this->getFullyOpinionatedFiles()
            ]
        ];
    }

    public function testDefaultOptions()
    {
        $generator = $this->getGenerator();
        $this->assertEmpty($generator->getPrefix());
        $this->assertEmpty($generator->getFixturesPath());
        $this->assertFalse($generator->hasSwagger());
        $this->assertFalse($generator->supportsTests());
    }

    public function testExplicitOptions()
    {
        $generator = $this->getGenerator(
            Format::YAML,
            self::getOutDir(),
            true,
            [
                'prefix' => 'api',
                'fixtures-path' => sys_get_temp_dir(),
                'swagger' => true,
                'tests' => true
            ]
        );

        $this->assertEquals('api', $generator->getPrefix());
        $this->assertEquals(sys_get_temp_dir(), $generator->getFixturesPath());
        $this->assertTrue($generator->hasSwagger());
        $this->assertTrue($generator->supportsTests());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @dataProvider badOptionsProvider
     *
     * @param array $badOptions
     */
    public function testBadOptionValues(array $badOptions)
    {
        $this->getGenerator(
            Format::YAML,
            self::getOutDir(),
            true,
            $badOptions
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testUndefinedOption()
    {
        $this->getGenerator(
            Format::YAML,
            self::getOutDir(),
            true,
            ['non-existent' => true]
        );
    }

    /**
     * @return array
     */
    public function badOptionsProvider()
    {
        return [
            [['prefix' => null]],
            [['prefix' => false]],
            [['prefix' => 1]],
            [['fixtures-path' => null]],
            [['fixtures-path' => false]],
            [['fixtures-path' => 1]],
            [['swagger' => null]],
            [['swagger' => '']],
            [['swagger' => 1]],
            [['tests' => null]],
            [['tests' => '']],
            [['tests' => 1]]
        ];
    }

    /**
     * @param bool $withTests
     *
     * @return File[]
     */
    private function getBasicFiles($withTests = false)
    {
        $files = [
            $this->getBasicControllerFileMock()->getRealPath() => $this->getBasicControllerFileMock()
        ];

        if ($withTests) {
            $files = array_merge($files, $this->getTestFiles());
        }

        return $files;
    }

    /**
     * @param bool $withTests
     *
     * @return File[]
     */
    private function getOpinionatedFiles($withTests = false)
    {
        $files = [
            $this->getOpinionatedControllerFileMock()->getRealPath() => $this->getOpinionatedControllerFileMock()
        ];

        if ($withTests) {
            $files = array_merge($files, $this->getTestFiles());
        }

        return $files;
    }

    /**
     * @param bool $withTests
     *
     * @return File[]
     */
    private function getFullyOpinionatedFiles($withTests = false)
    {
        $files = [
            $this->getFullyOpinionatedControllerFileMock()->getRealPath() =>
                $this->getFullyOpinionatedControllerFileMock()
        ];

        if ($withTests) {
            $files = array_merge($files, $this->getTestFiles());
        }

        return $files;
    }

    /**
     * @return File[]
     */
    private function getTestFiles()
    {
        return [
            $this->getAbstractControllerTestFileMock()->getRealPath() => $this->getAbstractControllerTestFileMock(),
            $this->getControllerTestFileMock()->getRealPath() => $this->getControllerTestFileMock()
        ];
    }

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return ControllerGenerator
     */
    protected function getGenerator(
        $format = Format::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new ControllerGenerator(
            $this->getMetadata(),
            $this->getBundle(),
            $this->getTemplateStrategy(),
            $format,
            $targetDir,
            $overwrite,
            $options,
            $forceGeneration
        );

        return $generator;
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
                    'getQueue'   => self::getStaticData('controller', 'BasicController.phps'),
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
                    'getQueue'   => self::getStaticData('controller', 'OpinionatedController.phps'),
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
                    'getQueue'  => self::getStaticData('controller', 'FullyOpinionatedController.phps'),
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
    protected function getAbstractControllerTestFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'  => self::getStaticData(
                        'controller' . DIRECTORY_SEPARATOR . 'test',
                        'AbstractControllerTest.phps'
                    ),
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Tests' .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'AbstractControllerTest.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }

    /**
     * @return File
     */
    protected function getControllerTestFileMock()
    {
        $controllerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $controllerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'  => self::getStaticData(
                        'controller' . DIRECTORY_SEPARATOR . 'test',
                        'FooControllerTest.phps'
                    ),
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Tests' .
                        DIRECTORY_SEPARATOR . 'Controller' .
                        DIRECTORY_SEPARATOR . 'FooControllerTest.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $controllerFileMock;
    }
}
