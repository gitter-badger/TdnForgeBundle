<?php

namespace Tdn\ForgeBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Traits\Bundled;
use Tdn\ForgeBundle\Traits\DoctrineMetadata;
use Tdn\ForgeBundle\Traits\FileDependencies;
use Tdn\ForgeBundle\Traits\Files;
use Tdn\ForgeBundle\Traits\Formattable;
use Tdn\ForgeBundle\Traits\OptionalDependency;
use Tdn\ForgeBundle\Traits\TargetedOutput;
use Tdn\ForgeBundle\Traits\OverWritable;
use Tdn\ForgeBundle\Traits\TemplateStrategy;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;

/**
 * Abstract Class AbstractGenerator
 *
 * Parent generator class. Each generator deals with a specific part
 * of a target application (controllers, managers, handlers, routing, etc)
 * When adding a new type of manipulation, a new generator should be
 * created extending this class.
 *
 * @package Tdn\ForgeBundle\Generator
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    use OverWritable;
    use Bundled;
    use TargetedOutput;
    use Formattable;
    use TemplateStrategy;
    use FileDependencies;
    use Files;
    use DoctrineMetadata;
    use OptionalDependency;

    /**
     * @var array|OptionsResolver[]
     */
    private static $resolversByClass = [];

    /**
     * @var ArrayCollection|PluginInterface[]
     */
    private $plugins;

    /**
     * @var ArrayCollection|string[]
     */
    private $messages;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool;
     */
    private $forge;

    /**
     * @var bool
     */
    private $ignoreOptionalDeps;

    /**
     * @param ClassMetadata $metadata
     * @param BundleInterface $bundle
     * @param TemplateStrategyInterface $templateStrategy
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forge
     * @param bool $ignoreOptionalDeps
     */
    public function __construct(
        ClassMetadata $metadata,
        BundleInterface $bundle,
        TemplateStrategyInterface $templateStrategy,
        $format,
        $targetDir,
        $overwrite,
        array $options,
        $forge = false,
        $ignoreOptionalDeps = false
    ) {
        $this->setMetadata($metadata);
        $this->setBundle($bundle);
        $this->setTemplateStrategy($templateStrategy);
        $this->setFormat($format);
        $this->setTargetDirectory($targetDir);
        $this->setOverWrite($overwrite);
        $this->setOptions($options);
        $this->setForge($forge);
        $this->setIgnoreOptionalDeps($ignoreOptionalDeps);

        $this->files            = new ArrayCollection();
        $this->fileDependencies = new ArrayCollection();
        $this->plugins          = new ArrayCollection();
        $this->messages         = new ArrayCollection();
    }

    /**
     * @return static
     */
    public function reset()
    {
        return new static(
            $this->getMetadata(),
            $this->getBundle(),
            $this->getTemplateStrategy(),
            $this->getFormat(),
            $this->getTargetDirectory(),
            $this->shouldOverWrite(),
            $this->getOptions(),
            $this->isForge()
        );
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [
            Format::YAML,
            Format::XML,
            Format::ANNOTATION
        ];
    }

    /**
     * @param Collection $plugins
     */
    public function setPlugins(Collection $plugins)
    {
        $this->plugins = new ArrayCollection();

        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }
    }

    /**
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin)
    {
            $this->plugins->add($plugin);
    }

    /**
     * @return ArrayCollection|PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param Collection $messages
     */
    public function setMessages(Collection $messages)
    {
        $this->messages = new ArrayCollection();

        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * @param string $message
     */
    public function addMessage($message)
    {
        $this->messages->add((string) $message);
    }

    /**
     * @return ArrayCollection|string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        $class = get_called_class();

        if (!isset(self::$resolversByClass[$class])) {
            self::$resolversByClass[$class] = new OptionsResolver();
            $this->configureOptions(self::$resolversByClass[$class]);
        }

        $this->options = $this->resolveOptions(self::$resolversByClass[$class], $options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param bool $forge
     */
    public function setForge($forge)
    {
        $this->forge = $forge;
    }

    /**
     * @return bool
     */
    public function isForge()
    {
        return $this->forge;
    }

    /**
     * @param bool $ignoreOptionalDeps
     */
    public function setIgnoreOptionalDeps($ignoreOptionalDeps)
    {
        $this->ignoreOptionalDeps = $ignoreOptionalDeps;
    }

    /**
     * @return bool
     */
    public function shouldIgnoreOptionalDeps()
    {
        return $this->ignoreOptionalDeps;
    }

    /**
     * Ensures the state of the bundle is valid for our generation purposes.
     *
     * Iterates through file dependencies and generated files to ensure
     * rules set against them pass. This should be always called if extending
     * this method with parent::isValid()
     *
     * @throws \RunTimeException
     * @return bool
     */
    public function isValid()
    {
        //Check to see if JMSDiExtraBundle exists.
        if ($this->getFormat() == Format::ANNOTATION
            && !class_exists('\\JMS\\DiExtraBundle\\JMSDiExtraBundle')
            && !$this->shouldIgnoreOptionalDeps()
        ) {
            throw $this->createOptionalDependencyMissingException('Please install JMSDiExtraBundle.');
        }

        foreach ($this->getPlugins() as $plugin) {
            foreach ($plugin->getFileDependencies() as $dependency) {
                if ($this->isDependencyValid($dependency)) {
                    continue;
                }
            }
        }

        foreach ($this->getFileDependencies() as $fileDependency) {
            if ($this->isDependencyValid($fileDependency)) {
                continue;
            }
        }

        foreach ($this->getFiles() as $generatedFile) {
            if ($this->isFileValid($generatedFile)) {
                continue;
            }
        }

        return true;
    }

    /**
     * Generates all the files declared by the generator if the
     * system is in a valid state.
     *
     * @return ArrayCollection|File[]
     */
    public function generate()
    {
        if ($this->isValid()) {
            foreach ($this->getPlugins() as $plugin) {
                $this->addFilesFromPlugin($plugin);
            }

            foreach ($this->getFiles() as $file) {
                //For service files and routing files, the file is regenerated completely. Writing to it
                //would duplicate content. So let's ensure the file is empty or..just delete it. Or if the overwrite
                //flag is set then let's go ahead and do it anyways.
                if (($file->isServiceFile() || $file->isAuxFile() || $this->shouldOverWrite()) && $file->isFile()) {
                    unlink($file->getRealPath()); //Remove before recreating
                }

                $this->getTemplateStrategy()->renderFile($file);
            }

            return $this->getFiles();
        }

        return new ArrayCollection();
    }

    /**
     * @param OptionsResolver $resolver
     * @param array $options
     *
     * @return array
     */
    protected function resolveOptions(OptionsResolver $resolver, array $options)
    {
        return $resolver->resolve($options);
    }

    /**
     * Override in child class to add options to resolver.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        return null;
    }

    /**
     * Ensures that dependency files exist
     *
     * Certain objects we're generating declare their dependencies
     * on other objects. This ensures those dependencies exist.
     *
     * @param File $file
     * @return bool
     */
    protected function isDependencyValid(File $file)
    {
        if (!$file->isReadable()) {
            throw new \RuntimeException(sprintf(
                'Please ensure the file %s exists and is readable.',
                $file->getRealPath()
            ));
        }

        return true;
    }

    /**
     * Ensures that files can be written without conflict
     *
     * Of if a conflict is present, that the class has been configured
     * to properly handle that conflict.
     *
     * @param File $file
     * @return bool
     */
    protected function isFileValid(File $file)
    {
        if (file_exists($file->getRealPath()) &&
            (!$this->shouldOverWrite() && !$file->isAuxFile() && !$file->isServiceFile())
        ) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the %s file as it already exists',
                $file->getRealPath()
            ));
        }

        return true;
    }

    /**
     * @param PluginInterface $plugin
     *
     * @throws \RuntimeException If there is an error
     *
     * @return void
     */
    private function addFilesFromPlugin(PluginInterface $plugin)
    {
        if ($plugin->isInstallable()) {
            foreach ($plugin->getFiles() as $file) {
                $this->addFile($file);
            }
        }
    }
}
