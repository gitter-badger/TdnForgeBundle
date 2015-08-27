<?php

namespace Tdn\ForgeBundle\Template\PostProcessor;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface PostProcessorInterface
 * @package Tdn\ForgeBundle\Template\PostProcessor
 */
interface PostProcessorInterface
{
    /**
     * @param SplFileInfo $file
     * @throws ProcessFailedException
     *
     * @return string The contents of the file after processing.
     */
    public function process(SplFileInfo $file);

    /**
     * @throws \RuntimeException when dependencies are not met.
     * @return bool
     */
    public function isValid();

    /**
     * @param SplFileInfo $file
     *
     * @return bool
     */
    public function supports(SplFileInfo $file);
}
