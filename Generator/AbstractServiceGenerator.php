<?php

namespace Tdn\ForgeBundle\Generator;

use Tdn\ForgeBundle\Services\Symfony\ServiceManager;
use Tdn\PhpTypes\Type\String;

/**
 * Abstract Class AbstractServiceGenerator
 *
 * Parent service generator. When a generator outputs service files (e.g. yaml or xml files)
 * for configuration purposes, it should extend this generator instead of directly inheriting
 * from AbstractGenerator. The Generator Factories take care of configuring the dependencies created
 * by interiting from this class.
 *
 * @package Tdn\ForgeBundle\Generator
 */
abstract class AbstractServiceGenerator extends AbstractGenerator implements ServiceGeneratorInterface
{
    /**
     * @var ServiceManager
     */
    private $serviceFileUtils;

    /**
     * @param ServiceManager $serviceFileUtils
     */
    public function setServiceManager(ServiceManager $serviceFileUtils)
    {
        $this->serviceFileUtils = $serviceFileUtils;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceFileUtils;
    }

    public function getServiceNamespace()
    {
        return (string) String::create($this->getBundle()->getName())
            ->underscored()
            ->toLowerCase()
            ->replace('bundle', '')
            ->removeRight('_')
        ;
    }

    protected function getServiceEntityName()
    {
        return (string) String::create($this->getEntity())->underscored()->toLowerCase();
    }
}
