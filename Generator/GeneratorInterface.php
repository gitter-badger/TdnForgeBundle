<?php

namespace Tdn\ForgeBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Traits\BundledInterface;
use Tdn\ForgeBundle\Traits\DoctrineMetadataInterface;
use Tdn\ForgeBundle\Traits\FormattableInterface;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Traits\OverWritableInterface;
use Tdn\ForgeBundle\Traits\TargetedOutputInterface;
use Tdn\ForgeBundle\Traits\HasTemplateStrategyInterface;

/**
 * Interface GeneratorInterface
 * @package Tdn\ForgeBundle\Generator
 */
interface GeneratorInterface extends
    DoctrineMetadataInterface,
    BundledInterface,
    HasTemplateStrategyInterface,
    FormattableInterface,
    TargetedOutputInterface,
    OverWritableInterface
{
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

    /**
     * @return GeneratorInterface
     */
    public function reset();
}
