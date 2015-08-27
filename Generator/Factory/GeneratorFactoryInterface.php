<?php

namespace Tdn\ForgeBundle\Generator\Factory;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Tdn\ForgeBundle\Generator\CommandGenerator;

/**
 * Interface GeneratorFactoryInterface
 * @package Tdn\ForgeBundle\Generator\Factory
 */
interface GeneratorFactoryInterface
{
    const TYPE_CONTROLLER_GENERATOR = 'controller';
    const TYPE_FORM_GENERATOR = 'form';
    const TYPE_HANDLER_GENERATOR = 'handler';
    const TYPE_MANAGER_GENERATOR = 'manager';
    const TYPE_ROUTING_GENERATOR = 'routing';

    /**
     * @return string
     */
    public static function getSupportedTypes();

    /**
     * @param string $type
     * @param ClassMetadata $metadata
     * @param BundleInterface $bundle
     * @param string $targetDir
     * @param string $format
     * @param bool $overwrite
     * @param array $options
     *
     * @return CommandGenerator
     */
    public function create(
        $type,
        ClassMetadata $metadata,
        BundleInterface $bundle,
        $targetDir,
        $format,
        $overwrite,
        array $options = []
    );
}
