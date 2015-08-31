<?php

namespace Tdn\ForgeBundle\Writer\PostProcessor;

use Symfony\Component\Process\Exception\ProcessFailedException;
use \SplFileInfo;

/**
 * Interface PostProcessorInterface
 * @package Tdn\ForgeBundle\Writer\PostProcessor
 */
interface PostProcessorInterface
{
    /**
     * @param SplFileInfo $file
     * @throws ProcessFailedException
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
