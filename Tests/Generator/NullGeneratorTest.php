<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Generator\NullGenerator;
use Tdn\ForgeBundle\Model\FormatInterface;

/**
 * Class NullGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
class NullGeneratorTest extends AbstractGeneratorTest
{
    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return GeneratorInterface
     */
    protected function getGenerator(
        $format = FormatInterface::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    ) {
        $generator = new NullGenerator(
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

    public function optionsProvider()
    {
        return [
            [
                FormatInterface::YAML,
                self::getOutDir(),
                false,
                [],
                []
            ],
            [
                FormatInterface::XML,
                self::getOutDir(),
                false,
                [],
                []
            ],
            [
                FormatInterface::ANNOTATION,
                self::getOutDir(),
                false,
                [],
                []
            ]
        ];
    }
}
