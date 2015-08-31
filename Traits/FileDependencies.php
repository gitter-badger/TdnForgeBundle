<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Model\File;

/**
 * Trait FileDependencies
 * @package Tdn\ForgeBundle\Traits
 */
trait FileDependencies
{
    /**
     * @var ArrayCollection|File[]
     */
    private $fileDependencies;

    /**
     * @param File $fileDependency
     */
    protected function addFileDependency(File $fileDependency)
    {
        if (!$this->fileDependencies->contains($fileDependency)) {
            $this->fileDependencies->add($fileDependency);
        }
    }

    /**
     * @return ArrayCollection|File[]
     */
    protected function getFileDependencies()
    {
        return $this->fileDependencies;
    }
}
