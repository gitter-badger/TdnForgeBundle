<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param Collection $fileDependencies
     */
    public function setFileDependencies(Collection $fileDependencies)
    {
        $this->fileDependencies = new ArrayCollection();

        foreach ($fileDependencies as $fileDependency) {
            $this->addFileDependency($fileDependency);
        }
    }

    /**
     * @param File $fileDependency
     */
    public function addFileDependency(File $fileDependency)
    {
        if (!$this->fileDependencies->contains($fileDependency)) {
            $this->fileDependencies->add($fileDependency);
        }
    }

    /**
     * @return ArrayCollection|File[]
     */
    public function getFileDependencies()
    {
        return $this->fileDependencies;
    }
}
