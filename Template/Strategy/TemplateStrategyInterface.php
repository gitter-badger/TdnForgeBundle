<?php

namespace Tdn\ForgeBundle\Template\Strategy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorInterface;

/**
 * Interface TemplateStrategyInterface
 * @package Tdn\ForgeBundle\Template\Strategy
 */
interface TemplateStrategyInterface
{
    /**
     * @param array|string[] $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs);

    /**
     * @param string $skeletonDir
     */
    public function addSkeletonDir($skeletonDir);

    /**
     * @return array|string[]
     */
    public function getSkeletonDirs();

    /**
     * @param string $template
     * @param array $parameters
     *
     * @return string
     */
    public function render($template, array $parameters);
}
