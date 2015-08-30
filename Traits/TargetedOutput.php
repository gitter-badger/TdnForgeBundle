<?php

namespace Tdn\ForgeBundle\Traits;

/**
 * Trait TargetedOutput
 * @package Tdn\ForgeBundle\Traits
 */
trait TargetedOutput
{
    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @param string $targetDirectory
     */
    protected function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return (is_dir($this->targetDirectory)) ? realpath($this->targetDirectory) : null;
    }
}
