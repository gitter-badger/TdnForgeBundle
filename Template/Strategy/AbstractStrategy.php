<?php

namespace Tdn\ForgeBundle\Template\Strategy;

use Tdn\ForgeBundle\TdnForgeBundle;
use Tdn\ForgeBundle\Template\PostProcessor\PostProcessorChain;
use Symfony\Component\Filesystem\Exception\IOException;
use Tdn\ForgeBundle\Model\File;

/**
 * Class AbstractStrategy
 * @package Tdn\ForgeBundle\Template\Strategy
 */
abstract class AbstractStrategy implements TemplateStrategyInterface
{
    /**
     * @var array|string[]
     */
    private $skeletonDirs;

    /**
     * @var PostProcessorChain
     */
    private $postProcessorChain;

    /**
     * @param PostProcessorChain $postProcessorChain
     */
    public function __construct(PostProcessorChain $postProcessorChain)
    {
        $this->postProcessorChain = $postProcessorChain;
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
     * Writes a file to disk and runs post processors on it.
     *
     * @param File $target
     *
     * @throws IOException
     */
    public function renderFile(File $target, $output = false)
    {
        if (!is_dir($target->getPath())) {
            mkdir($target->getPath(), 0755, true);
        }

        if (false === file_put_contents($target->getRealPath(), $target->getContent())) {
            throw new IOException(sprintf(
                'Could not write file %s. Reason: %s',
                $target->getRealPath(),
                error_get_last()
            ));
        }

        foreach ($this->postProcessorChain->getPostProcessorsForFile($target) as $postProcessor) {
            $postProcessor->process($target);
        }
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
