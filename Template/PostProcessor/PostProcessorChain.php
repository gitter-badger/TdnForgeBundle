<?php

namespace Tdn\ForgeBundle\Template\PostProcessor;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;

/**
 * Class PostProcessorChain
 * @package Tdn\ForgeBundle\Template\PostProcessor
 */
class PostProcessorChain
{
    /**
     * @var ArrayCollection|PostProcessorInterface[]
     */
    private $postProcessors;

    public function __construct()
    {
        $this->postProcessors = new ArrayCollection();
    }

    public function addPostProcessor(PostProcessorInterface $postProcessor)
    {
        $this->postProcessors->add($postProcessor);
    }

    /**
     * @param File $file
     * @return ArrayCollection|PostProcessorInterface[]
     */
    public function getPostProcessorsForFile(File $file)
    {
        $matching = new ArrayCollection();

        foreach ($this->postProcessors as $postProcessor) {
            if ($postProcessor->supports($file) && $postProcessor->isValid()) {
                $matching->add($postProcessor);
            }
        }

        return $matching;
    }
}
