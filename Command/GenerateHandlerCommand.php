<?php

namespace Tdn\ForgeBundle\Command;

use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;

/**
 * Class GenerateHandlerCommand
 *
 * Generates a REST handler that provide a way of managing your entities in a controller context.
 *
 * @package Tdn\ForgeBundle\Command
 */
class GenerateHandlerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'forge:generate:handler';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates an entity REST handler file for a controller.';

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_HANDLER_GENERATOR;
    }
}
