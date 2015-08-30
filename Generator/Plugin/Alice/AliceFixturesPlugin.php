<?php

namespace Tdn\ForgeBundle\Generator\Plugin\Controller;

use Tdn\ForgeBundle\ClassLoader\ClassMapGenerator;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Model\File;

/**
 * Class AliceFixturesPlugin
 * @package Tdn\ForgeBundle\Generator\Plugin\Controller
 */
class AliceFixturesPlugin implements PluginInterface
{
    /**
     * @return File[]
     */
    public function getFiles()
    {
        // TODO: Implement getFiles() method.
    }

    /**
     * @return File[]
     */
    public function getFileDependencies()
    {
        // TODO: Implement getFileDependencies() method.
    }

    /**
     * @return bool
     */
    public function isInstallable()
    {
        // TODO: Implement isInstallable() method.
    }


    /**
     * @return array
     */
    private function getAliceFixtures()
    {
        return array_keys(ClassMapGenerator::createMap($this->getFixturesPath()->getRealPath(), $this->getPathDepth()));
    }

    /**
     * @param string $fqdn
     * @return string
     */
    private function getClassFromFqdn($fqdn)
    {
        $fqdn = String::create($fqdn);

        if ($fqdn->strrpos('\\') !== false) {
            return (string) $fqdn->substr($fqdn->strrpos('\\') + 1);
        }

        return $fqdn;
    }

    /**
     * @param string $fqdn
     * @return string
     */
    private function getNamespaceFromFqdn($fqdn)
    {
        $fqdn = String::create($fqdn);

        return (string) $fqdn->substr(0, $fqdn->strrpos('\\'));
    }
}
