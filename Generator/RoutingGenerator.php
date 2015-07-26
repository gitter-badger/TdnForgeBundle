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
    public function setRoutingFile($routingFile)
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
    public function setPrefix($prefix)
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
     * Sets up routing file contents based on state for a specific entity (adding/removing).
     * @return $this
     */
    public function configure()
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

        return $this;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('routing-file')
            ->setAllowedTypes('routing-file', 'string')

            ->setRequired('prefix')
            ->setAllowedTypes('prefix', 'string')
        ;

        $resolver->setDefaults([
            'prefix' => '',
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
            $this->getRoutingContents($filePath)
        );

        //Needed because this might exist but we still want to add routes to it
        $routingFile->setAuxFile(true);

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

        return $this->getRoutingFileUtils()
            ->addRouteDefinition($routeDefinition)
            ->dump($filePath)
        ;
    }

    /**
     * @return string
     */
    protected function createRouteId()
    {
        return (string) String::create(self::API_PREFIX)
            ->ensureRight(
                ($this->getPrefix() && $this->getFormat() !== Format::ANNOTATION) ?
                $this->getEntity() . '_' . (string) String::create($this->getPrefix())->replace('/', '') :
                $this->getEntity()
            )
            ->toLowerCase();
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
    public function getRoutingFileUtils()
    {
        return new RoutingManager();
    }
}
