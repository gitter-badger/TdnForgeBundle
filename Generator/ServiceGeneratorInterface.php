<?php

namespace Tdn\ForgeBundle\Generator;

use Tdn\ForgeBundle\Services\Symfony\ServiceManager;

/**
 * Interface ServiceGeneratorInterface
 * @package Tdn\ForgeBundle\Generator
 */
interface ServiceGeneratorInterface extends GeneratorInterface
{
    /**
     * @param ServiceManager $serviceFileUtil
     */
    public function setServiceManager(ServiceManager $serviceFileUtil);

    /**
     * @return ServiceManager
     */
    public function getServiceManager();
}
