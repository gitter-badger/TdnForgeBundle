<?php

namespace Tdn\ForgeBundle\Traits;

/**
 * Interface TargetedOutputInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface TargetedOutputInterface
{
    /**
     * @param string $targetDirectory
     */
    public function setTargetDirectory($targetDirectory);

    /**
     * @return string
     */
    public function getTargetDirectory();
}
