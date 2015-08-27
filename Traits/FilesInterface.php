<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;

/**
 * Interface FilesInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface FilesInterface
{
    /**
     * @param Collection $files
     */
    public function setFiles(Collection $files);

    /**
     * @param File $file
     */
    public function addFile(File $file);

    /**
     * @return ArrayCollection|File[]
     */
    public function getFiles();
}
