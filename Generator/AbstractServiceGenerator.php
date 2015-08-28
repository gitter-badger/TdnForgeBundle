<?php

namespace Tdn\ForgeBundle\Generator;

use Tdn\ForgeBundle\Services\Symfony\ServiceManager;
use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\File;

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
     * Should parse the existing file and resolve any additions / configurations
     * of services to it. Returns the contents as a string.
     *
     * @param string $filePath
     *
     * @return string
     */
    abstract protected function getServiceFileContents($filePath);

    /**
     * Returns an new configured instance (useful after running generate if want to run again)
     *
     * @return ServiceGeneratorInterface
     */
    public function reset()
    {
        $new = new static(
            $this->getMetadata(),
            $this->getBundle(),
            $this->getTemplateStrategy(),
            $this->getFormat(),
            $this->getTargetDirectory(),
            $this->shouldOverWrite(),
            $this->getOptions(),
            $this->shouldForceGeneration(),
            $this->isForge()
        );

        if ($new instanceof ServiceGeneratorInterface) {
            $new->setServiceManager($this->getServiceManager());
        }

        return $new;
    }

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

    /**
     * @return string
     */
    public function getServiceBundleNamespace()
    {
        return (string) String::create($this->getBundle()->getName())
            ->underscored()
            ->toLowerCase()
            ->replace('bundle', '')
            ->removeRight('_')
        ;
    }

    /**
     * @return void
     */
    protected function addServiceFile($name)
    {
        $filePath = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Resources' .
            DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . '%s.%s',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $name,
            $this->getFormat()
        );

        $serviceFile = new File(
            $filePath,
            $this->getServiceFileContents($filePath),
            File::QUEUE_ALWAYS
        );

        $this->addMessage(sprintf(
            'Make sure to load "%s" in your extension file to enable the new services.',
            $serviceFile->getBasename()
        ));

        $this->addFile($serviceFile);
    }

    /**
     * @return string
     */
    protected function getServiceEntityName()
    {
        return (string) String::create($this->getEntity())->underscored()->toLowerCase();
    }
}
