<?php

namespace Tdn\ForgeBundle\Command;

use Tdn\ForgeBundle\Generator\GeneratorInterface;

/**
 * Interface GeneratorCommandInterface
 * @package Tdn\ForgeBundle\Command
 */
interface GeneratorCommandInterface
{
    /**
     * @param GeneratorInterface $generator
     */
    public function setGenerator(GeneratorInterface $generator);

    /**
     * @return GeneratorInterface
     */
    public function getGenerator();
}
