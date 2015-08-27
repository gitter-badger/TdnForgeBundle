<?php

namespace Tdn\ForgeBundle\Generator;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Traits\BundledInterface;
use Tdn\ForgeBundle\Traits\DoctrineMetadataInterface;
use Tdn\ForgeBundle\Traits\FileDependenciesInterface;
use Tdn\ForgeBundle\Traits\FilesInterface;
use Tdn\ForgeBundle\Traits\FormattableInterface;
use Tdn\ForgeBundle\Traits\OverWritableInterface;
use Tdn\ForgeBundle\Traits\TargetedOutputInterface;
use Tdn\ForgeBundle\Traits\TemplateStrategyInterface;

/**
 * Interface GeneratorInterface
 * @package Tdn\ForgeBundle\Generator
 */
interface GeneratorInterface extends
    CommandGenerator,
    BundledInterface,
    FormattableInterface,
    OverWritableInterface,
    TemplateStrategyInterface,
    TargetedOutputInterface,
    FileDependenciesInterface,
    FilesInterface,
    DoctrineMetadataInterface
{
    /**
     * @return static
     */
    public function reset();

    /**
     * Runs instantly upon instantiation.
     *
     * Sets up file dependencies, and internal file pointers
     * so they can be written by calling generate()
     *
     * @return $this
     */
    public function configure();

    /**
     * @param Collection $plugins
     */
    public function setPlugins(Collection $plugins);

    /**
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin);

    /**
     * @return ArrayCollection|PluginInterface[]
     */
    public function getPlugins();

    /**
     * @param Collection $messages
     */
    public function setMessages(Collection $messages);

    /**
     * @param string $message
     */
    public function addMessage($message);

    /**
     * @throws \RunTimeException
     * @return bool
     */
    public function isValid();
}
