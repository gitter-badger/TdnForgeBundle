<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Generator\NullGenerator;
use Tdn\ForgeBundle\Model\Format;

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
        $format = Format::YAML,
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
                Format::YAML,
                self::getOutDir(),
                false,
                [],
                []
            ],
            [
                Format::XML,
                self::getOutDir(),
                false,
                [],
                []
            ],
            [
                Format::ANNOTATION,
                self::getOutDir(),
                false,
                [],
                []
            ]
        ];
    }
}
