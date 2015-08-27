<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\HandlerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\Format;

/**
 * Class HandlerGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class HandlerGeneratorTest extends AbstractServiceGeneratorTest
{
    /**
     * @param array $options
     *
     * @return HandlerGenerator
     */
    protected function getGenerator(array $options = [])
    {
        $generator = new HandlerGenerator(
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

        $generator->setServiceManager($this->getServiceManager());

        return $generator;
    }

    public function optionsProvider()
    {
        return [
            [
                Format::YAML,
                true,
                $this->getYamlFiles()
            ],
            [
                Format::XML,
                true,
                $this->getXmlFiles()
            ],
            [
                Format::ANNOTATION,
                true,
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
     * @return ArrayCollection|File[]
     */
    protected function getFileDependencies()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            $this->getOutDir(),
            'Foo'
        );

        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Form' . DIRECTORY_SEPARATOR .
            'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            $this->getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($managerFile),
            new File($formType)
        ]);
    }

    /**
     * @return ArrayCollection
     */
    protected function getExpectedMessages()
    {
        return new ArrayCollection([
            sprintf(
                'Make sure to load "%s" in your extension file to enable the new services.',
                'handlers.' . $this->getGenerator()->getFormat()
            )
        ]);
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
                    'getContent'  => self::getStaticData('handler', 'Handler.phps'),
                    'getFilename'  => 'FooHandler',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
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
                    'getContent'   => self::getStaticData('handler', 'AnnotatedHandler.phps'),
                    'getFilename'  => 'FooHandler',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR . 'Handler',
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
                    'getContent'   => self::getStaticData('handler', 'handlers.xml'),
                    'getFilename'  => 'handlers',
                    'getExtension' => 'xml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
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
                    'getContent'   => self::getStaticData('handler', 'handlers.yaml'),
                    'getFilename'  => 'handlers',
                    'getExtension' => 'yaml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $handlrServMock;
    }
}
