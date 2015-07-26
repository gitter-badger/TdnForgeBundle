<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\ForgeBundle\Generator\ManagerGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\Format;

/**
 * Class ManagerGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class ManagerGeneratorTest extends AbstractServiceGeneratorTest
{
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

    /**
     * @param array $options
     *
     * @return ManagerGenerator
     */
    protected function getGenerator(array $options = [])
    {
        $generator = new ManagerGenerator(
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

    /**
     * @return ArrayCollection|SplFileInfo[]
     */
    protected function getFileDependencies()
    {
        return new ArrayCollection();
    }

    /**
     * @return ArrayCollection|string[]
     */
    protected function getExpectedMessages()
    {
        if ($this->getGenerator()->getFormat() == Format::XML || $this->getGenerator()->getFormat() == Format::YAML) {
            return new ArrayCollection([
                sprintf(
                    'Make sure to load "%s" in your extension file to enable the new services.',
                    'managers.' . $this->getGenerator()->getFormat()
                )
            ]);
        }

        return new ArrayCollection();
    }

    /**
     * @return array|File[]
     */
    public function getXmlFiles()
    {
        return [
            $this->getAbstractManagerFileMock()->getRealPath() => $this->getAbstractManagerFileMock(),
            $this->getManagerFileMock()->getRealPath() => $this->getManagerFileMock(),
            $this->getMgrInterfaceFileMock()->getRealPath() => $this->getMgrInterfaceFileMock(),
            $this->getXmlManagerServiceMock()->getRealPath() => $this->getXmlManagerServiceMock()
        ];
    }

    /**
     * @return array|File[]
     */
    public function getYamlFiles()
    {
        return [
            $this->getAbstractManagerFileMock()->getRealPath() => $this->getAbstractManagerFileMock(),
            $this->getManagerFileMock()->getRealPath() => $this->getManagerFileMock(),
            $this->getMgrInterfaceFileMock()->getRealPath() => $this->getMgrInterfaceFileMock(),
            $this->getYamlManagerServiceMock()->getRealPath() => $this->getYamlManagerServiceMock()
        ];
    }

    /**
     * @return array|File[]
     */
    public function getAnnotatedFiles()
    {
        return [
            $this->getAnnotatedAbstractManagerFileMock()->getRealPath() => $this->getAnnotatedAbstractManagerFileMock(),
            $this->getAnnotatedManagerFileMock()->getRealPath() => $this->getAnnotatedManagerFileMock(),
            $this->getMgrInterfaceFileMock()->getRealPath() => $this->getMgrInterfaceFileMock()
        ];
    }

    /**
     * @return File
     */
    protected function getAbstractManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'  => self::getStaticData('manager', 'AbstractManager.phps'),
                    'getFilename'  => 'AbstractManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'AbstractManager.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getAnnotatedAbstractManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'  => self::getStaticData('manager', 'AnnotatedAbstractManager.phps'),
                    'getFilename'  => 'AbstractManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'AbstractManager.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('manager', 'Manager.phps'),
                    'getFilename'  => 'FooManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getAnnotatedManagerFileMock()
    {
        $managerFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $managerFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('manager', 'AnnotatedManager.phps'),
                    'getFilename'  => 'FooManager',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getExtension' => 'php',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManager.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $managerFileMock;
    }

    /**
     * @return File
     */
    protected function getMgrInterfaceFileMock()
    {
        $mgrInterfaceFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrInterfaceFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('manager', 'ManagerInterface.phps'),
                    'getFilename'  => 'FooManagerInterface',
                    'getExtension' => 'php',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Entity' . DIRECTORY_SEPARATOR . 'Manager' . DIRECTORY_SEPARATOR . 'FooManagerInterface.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrInterfaceFileMock;
    }

    /**
     * @return File
     */
    protected function getXmlManagerServiceMock()
    {
        $mgrServiceMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('manager', 'managers.xml'),
                    'getFilename'  => 'managers',
                    'getExtension' => 'xml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.xml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }

    /**
     * @return File
     */
    protected function getYamlManagerServiceMock()
    {
        $mgrServiceMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $mgrServiceMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('manager', 'managers.yaml'),
                    'getFilename'  => 'managers',
                    'getExtension' => 'yaml',
                    'getPath'      => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
