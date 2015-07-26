<?php

namespace Tdn\ForgeBundle\Generator\Plugin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class AbstractPlugin
 * @package Tdn\ForgeBundle\Generator\Plugin
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Child plugins...
     * @var ArrayCollection|PluginInterface
     */
    protected $plugins;

    /**
     * @param Collection $plugins
     */
    protected function setPlugins(Collection $plugins)
    {
        $this->plugins = new ArrayCollection();

        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }
    }

    /**
     * @param PluginInterface $plugin
     */
    protected function addPlugin(PluginInterface $plugin)
    {
        $this->initializePlugins();
        $this->plugins->add($plugin);
    }

    /**
     * @return ArrayCollection|PluginInterface
     */
    protected function getPlugins()
    {
        $this->initializePlugins();
        return $this->plugins;
    }

    /**
     * Constructor might be different for each plugin.
     */
    private function initializePlugins()
    {
        if ($this->plugins == null) {
            $this->plugins = new ArrayCollection();
        }
    }
}
