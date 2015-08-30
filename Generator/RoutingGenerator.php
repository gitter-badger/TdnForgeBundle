<?php

namespace Tdn\ForgeBundle\Generator;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Tdn\ForgeBundle\Model\File;
use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\RouteDefinition;
use Tdn\ForgeBundle\Services\Symfony\RoutingManager;

/**
 * Class RoutingGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class RoutingGenerator extends AbstractGenerator
{
    const TYPE = "rest";
    const API_PREFIX = "api_";
    const DEFAULT_ROUTING_FILE = 'routing';

    /**
     * @var string
     */
    private $routingFile;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $routingFile
     */
    protected function setRoutingFile($routingFile)
    {
        $this->routingFile = $routingFile;
    }

    /**
     * @return string
     */
    public function getRoutingFile()
    {
        return $this->routingFile;
    }

    /**
     * @param string $prefix
     */
    protected function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Adds Routing File
     * Adds Controller Dependency
     */
    protected function configure()
    {
        $this->addRoutingFile();
        $this->addControllerDependency();

        if ($this->getFormat() == Format::ANNOTATION && !$this->isForge()) {
            $this->addMessage(
                sprintf(
                    'It is recommended that you run the %s command with the --format=annotation flag.',
                    'forge:generate:controller'
                )
            );
        }

        parent::configure();
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined('routing-file')
            ->setAllowedTypes('routing-file', 'string')

            ->setDefined('prefix')
            ->setAllowedTypes('prefix', 'string')
        ;

        $resolver->setDefaults([
            'prefix' => '',
            'routing-file' => self::DEFAULT_ROUTING_FILE
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @param array $options
     *
     * @return array
     */
    protected function resolveOptions(OptionsResolver $resolver, array $options)
    {
        $options = $resolver->resolve($options);
        $this->setRoutingFile($options['routing-file']);
        $this->setPrefix($options['prefix']);

        return $options;
    }

    /**
     * @return void
     */
    protected function addRoutingFile()
    {
        $filePath = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Resources' .
            DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . '%s.%s',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getRoutingFile(),
            ($this->getFormat() == Format::ANNOTATION) ? Format::YAML : $this->getFormat()
        );

        $routingFile = new File(
            $filePath,
            $this->getRoutingContents($filePath),
            File::QUEUE_ALWAYS
        );

        $this->addFile($routingFile);
    }

    /**
     * @param  string $filePath
     * @return string
     */
    protected function getRoutingContents($filePath)
    {
        $routeDefinition = new RouteDefinition(
            $this->createRouteId(),
            sprintf(
                "@%s/Controller/%sController.php",
                $this->getBundle()->getName(),
                $this->getEntity()
            ),
            ($this->getPrefix() && $this->getFormat() !== Format::ANNOTATION) ?
            (string) String::create($this->getPrefix())->replace('/', '') : "",
            self::TYPE
        );

        return $this->getRoutingManager()
            ->addRouteDefinition($routeDefinition)
            ->dump($filePath)
        ;
    }

    /**
     * @return string
     */
    protected function createRouteId()
    {
        return (string) String::create($this->getRouteNamespace())
            ->ensureRight(
                ($this->getPrefix() && $this->getFormat() !== Format::ANNOTATION) ?
                (string) String::create($this->getPrefix())->replace('/', '')->ensureLeft('_') : ''
            )
            ->ensureRight(
                '_' . strtolower($this->getEntity())
            )
            ->toLowerCase();
    }

    /**
     * @return string
     */
    protected function getRouteNamespace()
    {
        return (string) String::create($this->getBundle()->getName())
            ->toLowerCase()
            ->replace('bundle', '')
        ;
    }

    /**
     * @return void
     */
    protected function addControllerDependency()
    {
        $controllerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
            $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new File($controllerFile));
    }

    /**
     * @return RoutingManager
     */
    protected function getRoutingManager()
    {
        return new RoutingManager();
    }
}
