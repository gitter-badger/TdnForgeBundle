<?php

namespace Tdn\ForgeBundle\Template\Strategy;

use Tdn\ForgeBundle\TdnForgeBundle;

/**
 * Class AbstractTemplateStrategy
 * @package Tdn\ForgeBundle\Template\Strategy
 */
abstract class AbstractTemplateStrategy implements TemplateStrategyInterface
{
    /**
     * @var array|string[]
     */
    private $skeletonDirs;

    public function __construct()
    {
        $this->setSkeletonDirs([$this->getBundledSkeletonDir()]);
    }

    /**
     * @param array|string[] $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs)
    {
        $this->skeletonDirs = $skeletonDirs;
    }

    /**
     * @param string $skeletonDir
     */
    public function addSkeletonDir($skeletonDir)
    {
        $this->skeletonDirs[] = $skeletonDir;
    }

    /**
     * @return array|string[]
     */
    public function getSkeletonDirs()
    {
        return $this->skeletonDirs;
    }

    /**
     * Returns this bundle's skeleton dirs.
     *
     * @return string
     */
    private function getBundledSkeletonDir()
    {
        $reflClass = new \ReflectionClass(new TdnForgeBundle());
        $skeletonDir = realpath(dirname($reflClass->getFileName()) . '/Resources/skeleton');

        return $skeletonDir;
    }
}
