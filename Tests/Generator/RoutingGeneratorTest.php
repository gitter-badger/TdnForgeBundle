<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\RoutingGenerator;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;
use \Mockery;

/**
 * Class RoutingGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class RoutingGeneratorTest extends AbstractGeneratorTest
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
                Format::YAML,
                self::getOutDir(),
                true,
                ['prefix' => 'api'],
                [$this->getYamlRoutingFileMock()->getRealPath() => $this->getYamlRoutingFileMock()]
            ],
            [
                Format::XML,
                self::getOutDir(),
                true,
                ['prefix' => 'api'],
                [$this->getXmlRoutingFileMock()->getRealPath() => $this->getXmlRoutingFileMock()]
            ],
            [
                Format::ANNOTATION,
                self::getOutDir(),
                true,
                [],
                [$this->getAnnotatedRoutingFileMock()->getRealPath() => $this->getAnnotatedRoutingFileMock()]
            ]
        ];
    }

    public function testDefaultOptions()
    {
        $generator = $this->getGenerator();
        $this->assertEmpty($generator->getPrefix());
        $this->assertEquals(RoutingGenerator::DEFAULT_ROUTING_FILE, $generator->getRoutingFile());
    }

    public function testExplicitOptions()
    {
        $generator = $this->getGenerator(
            Format::YAML,
            self::getOutDir(),
            true,
            [
                'prefix' => 'api',
                'routing-file' => 'woot',
            ]
        );

        $this->assertEquals('api', $generator->getPrefix());
        $this->assertEquals('woot', $generator->getRoutingFile());
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
     * @return array
     */
    public function badOptionsProvider()
    {
        return [
            [['prefix' => null]],
            [['prefix' => false]],
            [['prefix' => 1]],
            [['routing-file' => null]],
            [['routing-file' => false]],
            [['routing-file' => 1]]
        ];
    }

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return RoutingGenerator
     */
    protected function getGenerator(
        $format = Format::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new RoutingGenerator(
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
    protected function getYamlRoutingFileMock()
    {
        $routingFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('routing', 'routing.yaml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }

    /**
     * We're just gonna default to yaml since we still need to load resources
     * this just moves one annotation to the controller.
     *
     * @return File
     */
    protected function getAnnotatedRoutingFileMock()
    {
        $routingFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('routing', 'annotated.yaml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }

    /**
     * @return File
     */
    protected function getXmlRoutingFileMock()
    {
        $routingFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $routingFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('routing', 'routing.xml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }
}
