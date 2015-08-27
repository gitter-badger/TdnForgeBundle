<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;

/**
 * Interface FileDependenciesInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface FileDependenciesInterface
{
    /**
     * @param Collection $fileDependencies
     */
    public function setFileDependencies(Collection $fileDependencies);

    /**
     * @param File $fileDependency
     */
    public function addFileDependency(File $fileDependency);

    /**
     * @return ArrayCollection|File[]
     */
    public function getFileDependencies();
}
