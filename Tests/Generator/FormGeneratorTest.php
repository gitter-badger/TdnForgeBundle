<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\FormGenerator;
use Tdn\ForgeBundle\Model\File;
use \Mockery;
use Tdn\ForgeBundle\Model\Format;

/**
 * Class FormGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class FormGeneratorTest extends AbstractGeneratorTest
{
    public function optionsProvider()
    {
        return [
            [
                Format::YAML,
                true,
                $this->getProcessedFiles()
            ],
            [
                Format::XML,
                true,
                $this->getProcessedFiles()
            ],
            [
                Format::ANNOTATION,
                true,
                $this->getProcessedFiles()
            ]
        ];
    }

    /**
     * @return File[]
     */
    protected function getProcessedFiles()
    {
        $formTypeFileMock  = $this->getFormTypeMock();
        $exceptionFileMock = $this->getExceptionFileMock();

        return [
            $formTypeFileMock->getRealPath()  => $formTypeFileMock,
            $exceptionFileMock->getRealPath() => $exceptionFileMock
        ];
    }

    /**
     * @param array $options
     *
     * @return FormGenerator
     */
    protected function getGenerator(array $options = [])
    {
        $generator = new FormGenerator(
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
     * @return ArrayCollection|File[]
     */
    protected function getFileDependencies()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            self::getOutDir(),
            'Foo'
        );

        return new ArrayCollection([
            new File($managerFile)
        ]);
    }

    /**
     * @return File
     */
    protected function getFormTypeMock()
    {
        $formTypeFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('form', 'FormType.phps'),
                    'getFilename'  => 'FooType',
                    'getExtension' => 'php',
                    'getPath'      => self::getOutDir() . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type',
                    'getRealPath'  => self::getOutDir() .
                        DIRECTORY_SEPARATOR . 'Form' .
                        DIRECTORY_SEPARATOR . 'Type' .
                        DIRECTORY_SEPARATOR . 'FooType.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $formTypeFileMock;
    }

    /**
     * @return File
     */
    protected function getExceptionFileMock()
    {
        $exceptionFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $exceptionFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => self::getStaticData('exception', 'FormException.phps'),
                    'getFilename'  => 'InvalidFormException',
                    'getExtension' => 'php',
                    'getPath'      => self::getOutDir() . DIRECTORY_SEPARATOR . 'Exception',
                    'getRealPath'  => self::getOutDir() .
                        DIRECTORY_SEPARATOR . 'Exception' .
                        DIRECTORY_SEPARATOR . 'InvalidFormException.php'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $exceptionFileMock;
    }
}
