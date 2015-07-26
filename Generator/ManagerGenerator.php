<?php

namespace Tdn\ForgeBundle\Generator;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\ServiceDefinition;

/**
 * Class ManagerGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class ManagerGenerator extends AbstractServiceGenerator
{
    /**
     * Sets up an Entity Manager based on entity.
     * Sets up an Entity Manager interface.
     * @return $this
     */
    public function configure()
    {
        $entityReflection = $this->getMetadata()->getReflectionClass();

        $entityConstructor = ($entityReflection) ?
            ($entityReflection->hasMethod('__construct')) ? $entityReflection->getMethod('__construct') : null : null;

        $path = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . 'Manager',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
        );

        $this->addAbstractManagerFile($path);
        $this->addManagerFile($path, $entityConstructor);
        $this->addManagerInterfaceFile($path, $entityConstructor);

        if ($this->getFormat() !== Format::ANNOTATION) {
            $this->addManagerServiceFile();
        }

        return $this;
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $entityConstructor
     */
    protected function addManagerFile($path, $entityConstructor = null)
    {
        $manager = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%sManager.php',
                $path,
                $this->getEntity()
            ),
            $this->getManagerContent($entityConstructor)
        );

        $this->addFile($manager);
    }

    /**
     * @param string $path
     */
    protected function addAbstractManagerFile($path)
    {
        $abstractManager = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'AbstractManager.php',
                $path
            ),
            $this->getAbstractManagerContent()
        );

        //File created only once unless there's an upgrade.
        if ((!$abstractManager->isFile() || $this->shouldOverWrite())
            || ($abstractManager->isDirty())) {
            if ($abstractManager->isDirty() && $abstractManager->isFile()) {
                $this->addMessage("Abstract manager was upgraded.");
            }

            $this->addFile($abstractManager);
        }
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $entityConstructor
     */
    protected function addManagerInterfaceFile($path, $entityConstructor = null)
    {
        $managerInterface = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%sManagerInterface.php',
                $path,
                $this->getEntity()
            ),
            $this->getManagerInterfaceContent($entityConstructor)
        );

        $managerInterface->setAuxFile(true);

        $this->addFile($managerInterface);
    }

    /**
     * @return void
     */
    protected function addManagerServiceFile()
    {
        $filePath = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Resources' .
            DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.%s',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getFormat()
        );

        $serviceFile = new File(
            $filePath,
            $this->getServiceFileContents($filePath)
        );

        $serviceFile->setServiceFile(true);

        $this->addMessage(sprintf(
            'Make sure to load "%s" in your extension file to enable the new services.',
            $serviceFile->getBasename()
        ));

        $this->addFile($serviceFile);
    }

    /**
     * Generates string containing params in following format:
     * Interface $param, array $param = [], $param...etc
     *
     * @param \ReflectionMethod|null $method
     * @return string
     */
    protected function getParams(\ReflectionMethod $method = null)
    {
        $outParams = '';
        if (null !== $method) {
            /** @var \ReflectionParameter $param */
            foreach ($method->getParameters() as $param) {
                $typeHint = $this->getTypeHintType($param);

                try {
                    $default = ' = ' . $param->getDefaultValue() . ', ';
                } catch (\ReflectionException $e) {
                    $default = ', ';
                }

                $outParams .= sprintf('%s$%s%s', $typeHint, $param->getName(), $default);
            }
        }

        return (string) String::create($outParams)->removeRight(', ');
    }

    /**
     * @param \ReflectionParameter|null $param
     * @return string
     */
    protected function getTypeHintType(\ReflectionParameter $param = null)
    {
        $hint = '';

        if ($param !== null) {
            if ($param->getClass() !== null) {
                return $param->getClass()->getName();
            }

            if ($param->isArray()) {
                return 'array';
            }

            if ($param->isCallable()) {
                return 'Callable';
            }
        }

        return $hint;
    }

    /**
     * Generates string containing params in following format:
     * $param1, $param2, $param3...etc
     *
     * @param \ReflectionMethod|null $method
     * @return string
     */
    protected function getConstructParams(\ReflectionMethod $method = null)
    {
        $params = '';

        if ($method) {
            /** @var \ReflectionParameter $param */
            foreach ($method->getParameters() as $param) {
                $params .= sprintf('$%s, ', $param->getName());
            }
        }

        return (string) String::create($params)->removeRight(', ');
    }

    /**
     * @return string
     */
    protected function getAbstractManagerContent()
    {
        return $this->getTemplateStrategy()->render(
            'manager/abstract-manager.php.twig',
            [
                'format'    => $this->getFormat(),
                'entity'    => $this->getEntity(),
                'namespace' => $this->getBundle()->getNamespace(),
            ]
        );
    }

    /**
     * @param \ReflectionMethod|null $constructorMethod
     * @return string
     */
    protected function getManagerContent(\ReflectionMethod $constructorMethod = null)
    {
        return $this->getTemplateStrategy()->render(
            'manager/manager.php.twig',
            [
                'entity'                  => $this->getEntity(),
                'entity_namespace'        => $this->getEntityNamespace(),
                'namespace'               => $this->getBundle()->getNamespace(),
                'format'                  => $this->getFormat(),
                'entity_construct_params' => $this->getParams($constructorMethod),
                'construct_params'        => $this->getConstructParams($constructorMethod),
                'service_id'              => $this->getServiceId()
            ]
        );
    }

    /**
     * @param \ReflectionMethod|null $constructorMethod
     * @return string
     */
    protected function getManagerInterfaceContent(\ReflectionMethod $constructorMethod = null)
    {
        return $this->getTemplateStrategy()->render(
            'manager/interface.php.twig',
            [
                'entity'                  => $this->getEntity(),
                'entity_namespace'        => $this->getEntityNamespace(),
                'namespace'               => $this->getBundle()->getNamespace(),
                'entity_construct_params' => $this->getParams($constructorMethod)
            ]
        );
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function getServiceFileContents($filePath)
    {
        $serviceClass = sprintf(
            '%s\\Entity\\Manager\\%sManager',
            $this->getBundle()->getNamespace(),
            $this->getEntity()
        );

        $paramKey = $this->getServiceClass();

        $serviceId = $this->getServiceId();

        $definition = new Definition('%' . $paramKey . '%');
        $definition
            ->addArgument(new Reference('doctrine'))
            ->addArgument(
                sprintf(
                    '%s\\Entity\\%s%s',
                    $this->getBundle()->getNamespace(),
                    $this->getEntityNamespace(),
                    $this->getEntity()
                )
            )
        ;

        return $this->getServiceManager()
            ->addParameter($paramKey, $serviceClass)
            ->addServiceDefinition(new ServiceDefinition($serviceId, $definition))
            ->dump($filePath)
        ;
    }

    /**
     * @return string
     */
    private function getServiceId()
    {
        return sprintf(
            '%s.entity.manager.%s_manager',
            $this->getServiceNamespace(),
            $this->getServiceEntityName()
        );
    }

    /**
     * @return string
     */
    private function getServiceClass()
    {
        return $this->getServiceId() . '.class';
    }
}
