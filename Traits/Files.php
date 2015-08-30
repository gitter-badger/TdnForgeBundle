<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;

/**
 * Trait Files
 * @package Tdn\ForgeBundle\Traits
 */
trait Files
{
    /**
     * @var ArrayCollection|File[]
     */
    private $files;

    /**
     * @param File $file
     */
    protected function addFile(File $file)
    {
        $this->files->set($file->getRealPath(), $file);
    }

    /**
     * @return ArrayCollection|File[]
     */
    protected function getFiles()
    {
        return $this->files;
    }
}
