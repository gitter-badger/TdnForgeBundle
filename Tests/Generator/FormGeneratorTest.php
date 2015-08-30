<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\FormGenerator;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;
use \Mockery;

/**
 * Class FormGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class FormGeneratorTest extends AbstractGeneratorTest
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
                [],
                $this->getProcessedFiles()
            ],
            [
                Format::XML,
                self::getOutDir(),
                true,
                [],
                $this->getProcessedFiles()
            ],
            [
                Format::ANNOTATION,
                self::getOutDir(),
                true,
                [],
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
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return FormGenerator
     */
    protected function getGenerator(
        $format = Format::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new FormGenerator(
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
    protected function getFormTypeMock()
    {
        $formTypeFileMock = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $formTypeFileMock
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'   => self::getStaticData('form', 'FormType.phps'),
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
                    'getQueue'   => self::getStaticData('exception', 'FormException.phps'),
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
