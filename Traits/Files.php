<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\Common\Collections\Collection;
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
     * @param Collection $files
     */
    public function setFiles(Collection $files)
    {
        $this->files = new ArrayCollection();

        foreach ($files as $generatedFile) {
            $this->addFile($generatedFile);
        }
    }

    /**
     * @param File $file
     */
    public function addFile(File $file)
    {
        $this->files->set($file->getRealPath(), $file);
    }

    /**
     * @return ArrayCollection|File[]
     */
    public function getFiles()
    {
        return $this->files;
    }
}
