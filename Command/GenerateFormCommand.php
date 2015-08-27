<?php

namespace Tdn\ForgeBundle\Command;

use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;

/**
 * Class GenerateFormCommand
 *
 * Generates a form type class for a given Doctrine entity, with optional REST generator support.
 *
 * @package Tdn\ForgeBundle\Command
 */
class GenerateFormCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'forge:generate:form';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a form type class based on a doctrine entity.';

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_FORM_GENERATOR;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Form type'];
    }
}
