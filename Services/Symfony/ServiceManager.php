<?php

namespace Tdn\ForgeBundle\Services\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\ServiceDefinition;

/**
 * Class ServiceManager
 * @package Tdn\ForgeBundle\ServiceFileLoader\Symfony
 */
class ServiceManager extends AbstractFileUtils
{
    /**
     * @var ArrayCollection
     */
    protected $parameters;

    /**
     * @var ArrayCollection|ServiceDefinition[]
     */
    protected $serviceDefinitions;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
        $this->serviceDefinitions = new ArrayCollection();
        $this->container = new ContainerBuilder();
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param ServiceDefinition $serviceDefinition
     *
     * @return $this
     */
    public function addServiceDefinition(ServiceDefinition $serviceDefinition)
    {
        $this->serviceDefinitions->add($serviceDefinition);

        return $this;
    }

    /**
     * @return ArrayCollection|ServiceDefinition[]
     */
    public function getServiceDefinitions()
    {
        return $this->serviceDefinitions;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function dump($file)
    {
        $file = new File($file);
        $container = $this->getResolvedContainer($file);

        return $this->getDumperFactory()->createDumper($this->getFormat($file), $container)->dump();
    }

    /**
     * @param File $file
     *
     * @return ContainerBuilder
     */
    protected function getResolvedContainer(File $file)
    {
        if ($file->isFile() && $file->isReadable()) {
            $loader = $this->getLoaderFactory()->createFileLoader(
                $this->getFormat($file),
                $this->container,
                $file->getPath()
            );
            $loader->load($file->getBasename());
        }

        foreach ($this->parameters as $paramK => $paramV) {
            $this->container->setParameter($paramK, $paramV);
        }

        foreach ($this->serviceDefinitions as $serviceDefinition) {
            $this->container->setDefinition($serviceDefinition->getId(), $serviceDefinition->getDefinition());
        }

        return $this->container;
    }

    /**
     * @return StandardLoaderFactory
     */
    protected function getLoaderFactory()
    {
        return new StandardLoaderFactory();
    }

    /**
     * @return StandardDumperFactory
     */
    protected function getDumperFactory()
    {
        return new StandardDumperFactory();
    }

    /**
     * @return SysTempFileFactory
     */
    protected function getTempFileFactory()
    {
        return new SysTempFileFactory();
    }
}
