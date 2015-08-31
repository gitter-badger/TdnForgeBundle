<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\HandlerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\FormatInterface;

/**
 * Class HandlerGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class HandlerGeneratorTest extends AbstractServiceGeneratorTest
{

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\CoreDependencyMissingException
     * @expectedExceptionMessageRegExp /Please ensure the file (.*) exists and is readable./
     */
    public function testDependencyMissing()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            false,
            [],
            false
        );

        $generator->generate();
    }

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return HandlerGenerator
     */
    protected function getGenerator(
        $format = FormatInterface::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new HandlerGenerator(
            $this->getMetadata(),
            $this->getBundle(),
            $this->getTemplateStrategy(),
            $format,
            $targetDir,
            $overwrite,
            $options,
            $forceGeneration
        );

        $generator->setServiceManager($this->getServiceManager());

        return $generator;
    }

    public function optionsProvider()
    {
        return [
            [
                FormatInterface::YAML,
                self::getOutDir(),
                true,
                [],
                $this->getYamlFiles()
            ],
            [
                FormatInterface::XML,
                self::getOutDir(),
                true,
                [],
                $this->getXmlFiles()
            ],
            [
                FormatInterface::ANNOTATION,
                self::getOutDir(),
                true,
                [],
                $this->getAnnotatedFiles()
            ]
        ];
    }

    public function getYamlFiles()
    {
        return [
            $this->getYamlHandlerServiceMock()->getRealPath() => $this->getYamlHandlerServiceMock(),
            $this->getHandlerFileMock()->getRealPath() => $this->getHandlerFileMock()
        ];
    }

    public function getXmlFiles()
    {
        return [
            $this->getXmlHandlerServiceMock()->getRealPath() => $this->getXmlHandlerServiceMock(),
            $this->getHandlerFileMock()->getRealPath() => $this->getHandlerFileMock()
        ];
    }

    public function getAnnotatedFiles()
    {
        return [
            $this->getAnnotatedHandlerFileMock()->getRealPath() => $this->getAnnotatedHandlerFileMock()
        ];
    }

    /**
     * @return ArrayCollection
     */
    protected function getExpectedMessages()
    {
        if ($this->getGenerator()->getFormat() !== FormatInterface::ANNOTATION) {
            return new ArrayCollection([
                sprintf(
                    'Make sure to load "%s" in your extension file to enable the new services.',
                    'handlers.' . $this->getGenerator()->getFormat()
                )
            ]);
        }

        return new ArrayCollection();

    }

    /**
     * @return File
     */
    protected function getHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'  => self::getStaticData('handler', 'Handler.phps'),
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return File
     */
    protected function getAnnotatedHandlerFileMock()
    {
        $handlerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('handler', 'AnnotatedHandler.phps'),
                    'getRealPath'  => $this->getOutDir() .
                        DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . 'FooHandler.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlerFileMock;
    }

    /**
     * @return File
     */
    protected function getXmlHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('handler', 'handlers.xml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }

    /**
     * @return File
     */
    protected function getYamlHandlerServiceMock()
    {
        $handlrServMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $handlrServMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('handler', 'handlers.yaml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
