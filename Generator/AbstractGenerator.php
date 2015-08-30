<?php

namespace Tdn\ForgeBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tdn\ForgeBundle\Exception\FileQueueOverwriteException;
use Tdn\ForgeBundle\Exception\PluginInstallException;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Traits\Bundled;
use Tdn\ForgeBundle\Traits\CoreDependency;
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
    use CoreDependency;

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
     * @var bool
     */
    private $forceGeneration;

    /**
     * @var bool;
     */
    private $forge;

    /**
     * @var bool
     */
    private $configured = false;

    /**
     * @param ClassMetadata $metadata
     * @param BundleInterface $bundle
     * @param TemplateStrategyInterface $templateStrategy
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     * @param bool $forge
     */
    public function __construct(
        ClassMetadata $metadata,
        BundleInterface $bundle,
        TemplateStrategyInterface $templateStrategy,
        $format,
        $targetDir,
        $overwrite,
        array $options,
        $forceGeneration = false,
        $forge = false
    ) {
        $this->setMetadata($metadata);
        $this->setBundle($bundle);
        $this->setTemplateStrategy($templateStrategy);
        $this->setFormat($format);
        $this->setTargetDirectory($targetDir);
        $this->setOverWrite($overwrite);
        $this->setOptions($options);
        $this->setForceGeneration($forceGeneration);
        $this->setForge($forge);

        $this->files            = new ArrayCollection();
        $this->fileDependencies = new ArrayCollection();
        $this->plugins          = new ArrayCollection();
        $this->messages         = new ArrayCollection();
    }

    /**
     * Returns an new configured instance (useful after running generate if want to run again)
     *
     * @return GeneratorInterface
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
            $this->shouldForceGeneration(),
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
     * Decorate in child class to add dependencies, add
     *
     * @return void
     */
    protected function configure()
    {
        $this->setConfigured(true);
    }

    /**
     * @param PluginInterface $plugin
     */
    protected function addPlugin(PluginInterface $plugin)
    {
            $this->plugins->add($plugin);
    }

    /**
     * @return ArrayCollection|PluginInterface[]
     */
    protected function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param string $message
     */
    protected function addMessage($message)
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
     * @param File $file
     */
    protected function addFile(File $file)
    {
        //We'll add what we can, and notify the user of anything that can't be generated.
        try {
            if ($this->isFileValid($file)) {
                $this->files->set($file->getRealPath(), $file);
            }
        } catch (FileQueueOverwriteException $e) {
            $this->addMessage($e->getMessage());
        }
    }

    /**
     * @param array $options
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     * @throws MissingOptionsException   If a required option is missing
     * @throws OptionDefinitionException If there is a cyclic dependency between
     *                                   lazy options and/or normalizers
     * @throws NoSuchOptionException     If a lazy option reads an unavailable option
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
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * @param bool $forceGeneration
     */
    protected function setForceGeneration($forceGeneration)
    {
        $this->forceGeneration = $forceGeneration;
    }

    /**
     * @return bool
     */
    protected function shouldForceGeneration()
    {
        return $this->forceGeneration;
    }

    /**
     * @param bool $forge
     */
    protected function setForge($forge)
    {
        $this->forge = $forge;
    }

    /**
     * @return bool
     */
    protected function isForge()
    {
        return $this->forge;
    }

    /**
     * @param bool $configured
     */
    protected function setConfigured($configured)
    {
        $this->configured = $configured;
    }

    /**
     * @return bool
     */
    protected function isConfigured()
    {
        return $this->configured;
    }

    /**
     * Ensures the state of the bundle is valid for our generation purposes.
     *
     * Iterates through file dependencies and proposed files to ensure
     * rules set against them pass. This should be always called if extending
     * this method with parent::isValid()
     *
     * @throws \RunTimeException
     * @return bool
     */
    protected function isValid()
    {
        //Check to see if JMSDiExtraBundle exists.
        if ($this->getFormat() == Format::ANNOTATION
            && !class_exists('\\JMS\\DiExtraBundle\\JMSDiExtraBundle')
            && !$this->shouldForceGeneration()
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
        if (!$this->isConfigured()) {
            $this->configure();
        }

        if ($this->isValid()) {
            foreach ($this->getPlugins() as $plugin) {
                $this->addFilesFromPlugin($plugin);
            }
        }

        return $this->getFiles();
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
        $resolver->setDefaults([]);
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
        if (!$file->isReadable() && !$this->shouldForceGeneration()) {
            throw $this->createCoreDependencyMissingException(sprintf(
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
     *
     * @return bool
     */
    protected function isFileValid(File $file)
    {
        if ($file->isReadable() && !$this->shouldOverWrite()) {
            switch ($file->getQueueType()) {
                case File::QUEUE_IF_UPGRADE:
                    if ($file->isDirty()) {
                        $this->addMessage(
                            sprintf(
                                "%s was upgraded.",
                                $file->getBasename('.' . $file->getExtension())
                            )
                        );
                        return true;
                    }

                    return false;
                case File::QUEUE_ALWAYS:
                    return true;
                default:
                case File::QUEUE_DEFAULT:
                    throw new FileQueueOverwriteException(sprintf(
                        'Unable to generate queue for %s as file as it already exists. To overwrite use --overwrite.',
                        $file->getRealPath()
                    ));
            }
        }

        return true;
    }

    /**
     * @param PluginInterface $plugin
     *
     * @throws PluginInstallException If there is an error
     *
     * @return void
     */
    private function addFilesFromPlugin(PluginInterface $plugin)
    {
        try {
            if ($plugin->isInstallable()) {
                foreach ($plugin->getFiles() as $file) {
                    $this->addFile($file);
                }
            }
        } catch (PluginInstallException $e) {
            if ($this->shouldForceGeneration()) {
                //Generate the files anyways.
                foreach ($plugin->getFiles() as $file) {
                    $this->addFile($file);
                }

                $this->addMessage($e->getMessage());
                return;
            }

            throw $e;
        }
    }
}
