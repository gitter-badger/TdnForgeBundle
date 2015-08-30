<?php

namespace Tdn\ForgeBundle\Command;

/**
 * Class GenerateFixturesCommand
 * @package Tdn\ForgeBundle\Command
 */
class GenerateFixturesCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'forge:generate:fixtures';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates an Alice fixture for the given entity.';

    /**
     * @return string
     */
    public function getType()
    {
        return '';
    }
}
