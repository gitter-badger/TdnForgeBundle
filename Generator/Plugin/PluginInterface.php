<?php

namespace Tdn\ForgeBundle\Generator\Plugin;

use Tdn\ForgeBundle\Exception\PluginInstallException;
use Tdn\ForgeBundle\Model\File;

/**
 * Interface PluginInterface
 * @package Tdn\ForgeBundle\Generator\Plugin
 */
interface PluginInterface
{
    /**
     * @return File[]
     */
    public function getFiles();

    /**
     * @return File[]
     */
    public function getFileDependencies();

    /**
     * @throws PluginInstallException when criteria is not met.
     *
     * @return bool
     */
    public function isInstallable();
}
