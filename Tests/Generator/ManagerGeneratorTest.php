<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
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
                self::getOutDir(),
                false,
                [],
                $this->getYamlFiles()
            ],
            [
                Format::XML,
                self::getOutDir(),
                false,
                [],
                $this->getXmlFiles()
            ],
            [
                Format::ANNOTATION,
                self::getOutDir(),
                false,
                [],
                $this->getAnnotatedFiles()
            ],
            [
                Format::YAML,
                self::getOutDir(),
                true,
                [],
                $this->getYamlFiles()
            ],
            [
                Format::XML,
                self::getOutDir(),
                true,
                [],
                $this->getXmlFiles()
            ],
            [
                Format::ANNOTATION,
                self::getOutDir(),
                true,
                [],
                $this->getAnnotatedFiles()
            ]
        ];
    }

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return ManagerGenerator
     */
    protected function getGenerator(
        $format = Format::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new ManagerGenerator(
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

    /**
     * @return ArrayCollection|string[]
     */
    protected function getExpectedMessages()
    {
        if ($this->getGenerator()->getFormat() !== Format::ANNOTATION) {
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
                    'getQueue'  => self::getStaticData('manager', 'AbstractManager.phps'),
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
                    'getQueue'  => self::getStaticData('manager', 'AnnotatedAbstractManager.phps'),
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
                    'getQueue'   => self::getStaticData('manager', 'Manager.phps'),
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
                    'getQueue'   => self::getStaticData('manager', 'AnnotatedManager.phps'),
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
                    'getQueue'   => self::getStaticData('manager', 'ManagerInterface.phps'),
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
                    'getQueue'   => self::getStaticData('manager', 'managers.xml'),
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
                    'getQueue'   => self::getStaticData('manager', 'managers.yaml'),
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR .
                        'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.yaml'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $mgrServiceMock;
    }
}
