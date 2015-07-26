<?php

namespace Tdn\ForgeBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;

/**
 * Interface CommandGenerator
 * @package Tdn\ForgeBundle\Generator
 */
interface CommandGenerator
{
    /**
     * Get files to generate without writing them.
     *
     * @return ArrayCollection|File[]
     */
    public function getFiles();

    /**
     * Write files to disk using the given template strategy.
     *
     * @return ArrayCollection|File[]
     */
    public function generate();

    /**
     * Get post-generate messages.
     *
     * @return ArrayCollection|string[]
     */
    public function getMessages();
}
