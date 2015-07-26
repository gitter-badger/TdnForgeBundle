<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\RoutingGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\Format;

/**
 * Class RoutingGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class RoutingGeneratorTest extends AbstractGeneratorTest
{
    public function testRoutingFile()
    {
        $generator = $this->getGenerator();
        $this->assertEquals($this->getDefaultOptions()['routing-file'], $generator->getRoutingFile());
        $generator->setRoutingFile('test.yaml');
        $this->assertEquals('test.yaml', $generator->getRoutingFile());
    }

    public function testRoutePrefix()
    {
        $generator = $this->getGenerator();
        $this->assertEquals($this->getDefaultOptions()['prefix'], $generator->getPrefix());
        $generator->setPrefix('api');
        $this->assertEquals('api', $generator->getPrefix());
    }

    public function optionsProvider()
    {
        return [
            [
                Format::YAML,
                true,
                [$this->getYamlRoutingFileMock()->getRealPath() => $this->getYamlRoutingFileMock()],
                $this->getDefaultOptions()
            ],
            [
                Format::XML,
                true,
                [$this->getXmlRoutingFileMock()->getRealPath() => $this->getXmlRoutingFileMock()],
                $this->getDefaultOptions()
            ],
            [
                Format::ANNOTATION,
                true,
                [$this->getAnnotatedRoutingFileMock()->getRealPath() => $this->getAnnotatedRoutingFileMock()],
                [
                    'routing-file' => 'routing',
                    'prefix' => ''
                ]

            ]
        ];
    }

    /**
     * @param array $options
     *
     * @return RoutingGenerator
     */
    protected function getGenerator(array $options = [])
    {
        $options = (!empty($options)) ? $options : $this->getDefaultOptions();
        $generator = new RoutingGenerator(
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
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'routing-file' => 'routing',
            'prefix' => 'v1'
        ];
    }

    /**
     * @return ArrayCollection|File[]
     */
    protected function getFileDependencies()
    {
        $controllerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($controllerFile, null, null)
        ]);
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
                    'getContent'   => self::getStaticData('routing', 'routing.yaml'),
                    'getFilename'  => 'routing',
                    'getExtension' => 'yaml',
                    'isAuxFile'    => true,
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
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
                    'getContent'   => self::getStaticData('routing', 'annotated.yaml'),
                    'getFilename'  => 'routing',
                    'getExtension' => 'yaml',
                    'isAuxFile'    => true,
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
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
                    'getContent'   => self::getStaticData('routing', 'routing.xml'),
                    'getFilename'  => 'routing',
                    'getExtension' => 'xml',
                    'isAuxFile'    => true,
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routing.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $routingFileMock;
    }
}
