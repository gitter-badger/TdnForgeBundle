<?php

namespace Tdn\ForgeBundle\Writer\Strategy;

use Tdn\ForgeBundle\Model\File;

/**
 * Interface WriterStrategyInterface
 * @package Tdn\ForgeBundle\Writer\Strategy
 */
interface WriterStrategyInterface
{
    /**
     * @param File $file
     *
     * @throw IOException if there is an error writing a file.
     *
     * @return void
     */
    public function writeFile(File $file);
}
