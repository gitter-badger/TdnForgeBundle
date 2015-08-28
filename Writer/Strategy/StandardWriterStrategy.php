<?php

namespace Tdn\ForgeBundle\Writer\Strategy;

use Symfony\Component\Filesystem\Exception\IOException;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorChain;

/**
 * Class StandardWriterStrategy
 * @package Tdn\ForgeBundle\Writer\Strategy
 */
class StandardWriterStrategy implements WriterStrategyInterface
{
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
    }

    /**
     * Writes a file to disk and runs post processors on it.
     * Will delete previous file if already exists.
     *
     * @param File $target
     *
     * @throws IOException
     */
    public function writeFile(File $target)
    {
        if (!is_dir($target->getPath())) {
            mkdir($target->getPath(), 0755, true);
        }

        if ($target->isFile()) {
            @unlink($target->getRealPath());
        }

        if (false === @file_put_contents($target->getRealPath(), $target->getQueue())) {
            throw new IOException(sprintf(
                'Could not write file %s. Reason: %s.',
                $target->getRealPath(),
                implode(', ', error_get_last())
            ));
        }

        foreach ($this->postProcessorChain->getPostProcessorsForFile($target) as $postProcessor) {
            $postProcessor->process($target);
        }
    }
}
