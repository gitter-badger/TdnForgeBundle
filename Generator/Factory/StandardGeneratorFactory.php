<?php

namespace Tdn\ForgeBundle\Generator\Factory;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\ForgeBundle\Generator\ServiceGeneratorInterface;
use Tdn\ForgeBundle\Generator\CommandGenerator;
use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Services\Symfony\ServiceManager;

/**
 * Class StandardGeneratorFactory
 * @package Tdn\ForgeBundle\Generator\Factory
 */
class StandardGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * Map to generators (fqdn)
     *
     * @var array
     */
    protected $generatorMap = [
        self::TYPE_CONTROLLER_GENERATOR => '\Tdn\ForgeBundle\Generator\ControllerGenerator',
        self::TYPE_FORM_GENERATOR       => '\Tdn\ForgeBundle\Generator\FormGenerator',
        self::TYPE_HANDLER_GENERATOR    => '\Tdn\ForgeBundle\Generator\HandlerGenerator',
        self::TYPE_MANAGER_GENERATOR    => '\Tdn\ForgeBundle\Generator\ManagerGenerator',
        self::TYPE_ROUTING_GENERATOR    => '\Tdn\ForgeBundle\Generator\RoutingGenerator'
    ];

    /**
     * @var TemplateStrategyInterface
     */
    private $templateStrategy;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param ServiceManager $serviceManager
     */
    public function __construct(TemplateStrategyInterface $templateStrategy, ServiceManager $serviceManager)
    {
        $this->templateStrategy = $templateStrategy;
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return array|string[]
     */
    public static function getSupportedTypes()
    {
        return [
            self::TYPE_CONTROLLER_GENERATOR,
            self::TYPE_FORM_GENERATOR,
            self::TYPE_HANDLER_GENERATOR,
            self::TYPE_MANAGER_GENERATOR,
            self::TYPE_ROUTING_GENERATOR
        ];
    }

    /**
     * @param string $type
     * @param ClassMetadata $metadata
     * @param BundleInterface $bundle
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     *
     * @return CommandGenerator
     */
    public function create(
        $type,
        ClassMetadata $metadata,
        BundleInterface $bundle,
        $format,
        $targetDir,
        $overwrite,
        array $options = []
    ) {
        $class = $this->getClassFromType($type);

        return $this->getServiceGenerator(
            new $class(
                $metadata,
                $bundle,
                $this->templateStrategy,
                $format,
                $targetDir,
                $overwrite,
                $options
            )
        )->configure();
    }

    /**
     * @param CommandGenerator $generator
     *
     * @return ServiceGeneratorInterface
     */
    private function getServiceGenerator(CommandGenerator $generator)
    {
        if ($generator instanceof ServiceGeneratorInterface) {
            $generator->setServiceManager($this->serviceManager);
        }

        return $generator;
    }

    /**
     * @param $type
     *
     * @return string
     */
    private function getClassFromType($type)
    {
        if (!isset($this->generatorMap[$type])) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid type '%s'. Suppored types are: %s.",
                    $type,
                    implode(', ', self::getSupportedTypes())
                )
            );
        }

        return $this->generatorMap[$type];
    }
}
