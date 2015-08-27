<?php

namespace Tdn\ForgeBundle\Command;

use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;

/**
 * Class GenerateManagerCommand
 *
 * Generates a entity manager specific for an entity (DAO)
 * with a repository as a dependency.
 *
 * @package Tdn\ForgeBundle\Command
 */
class GenerateManagerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'forge:generate:manager';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates an entity manager (Repository + DAO patterns) for a given entity.';

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_MANAGER_GENERATOR;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Entity Manager', 'Entity Manager Interface'];
    }
}
