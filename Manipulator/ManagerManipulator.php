<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\GeneratedFile;
use Tdn\PilotBundle\Model\GeneratedFileInterface;

/**
 * Class ManagerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class ManagerManipulator extends AbstractServiceManipulator
{
    /**
     * Sets up an Entity Manager based on entity.
     * Sets up an Entity Manager interface.
     * @return $this
     */
    public function prepare()
    {
        $entityReflection = $this->getMetadata()->getReflectionClass();

        $constructorMethod = ($entityReflection) ?
            ($entityReflection->hasMethod('__construct')) ? $entityReflection->getMethod('__construct') : null : null;

        $path = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . 'Manager',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
        );

        $this->addManagerFile($path, $constructorMethod);
        $this->addManagerInterfaceFile($path, $constructorMethod);
        $this->addManagerServiceFile();
        $this->setUpdatingDiConfFile(true);

        return $this;
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $constructorMethod
     */
    protected function addManagerFile($path, $constructorMethod = null)
    {
        $manager = new GeneratedFile();
        $manager
            ->setFilename($this->getEntity() . 'Manager')
            ->setExtension('php')
            ->setPath($path)
            ->setContents($this->getManagerContent($constructorMethod))
        ;

        $this->addGeneratedFile($manager);
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $constructorMethod
     */
    protected function addManagerInterfaceFile($path, $constructorMethod = null)
    {
        $managerInterface = new GeneratedFile();
        $managerInterface
            ->setFilename($this->getEntity() . 'ManagerInterface')
            ->setExtension('php')
            ->setPath($path)
            ->setContents($this->getManagerInterfaceContent($constructorMethod))
            ->setAuxFile(true)
        ;

        $this->addGeneratedFile($managerInterface);
    }

    /**
     * @return void
     */
    protected function addManagerServiceFile()
    {
        $serviceFile = new GeneratedFile();
        $serviceFile
            ->setFilename('managers')
            ->setExtension('xml')
            ->setPath(sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            ))
            ->setContents($this->getServiceFileContents($serviceFile)) //Kinda bad...fix later (Needs to be called last)
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in the %s file to enable the new services.',
            $serviceFile->getFilename() . '.' . $serviceFile->getExtension(),
            $this->getDefaultExtensionFile()
        ));

        $this->addGeneratedFile($serviceFile);
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
                'entity_construct_params' => $this->getParams($constructorMethod),
                'construct_params'        => $this->getConstructParams($constructorMethod)
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
     * @param GeneratedFileInterface $managerFile
     *
     * @return string
     */
    protected function getServiceFileContents(GeneratedFileInterface $managerFile)
    {
        $serviceClass = sprintf(
            '%s\\Entity\\Manager\\%sManager',
            $this->getBundle()->getNamespace(),
            $this->getEntity()
        );

        $paramKey = sprintf(
            '%s.%s.manager.class',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $serviceId = sprintf(
            '%s.%s.manager',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $this->setXmlServiceFile($managerFile);
        $newXml = $this->getXmlServiceFile();
        $this->getDiUtils()->setDiXmlTags($newXml, $serviceClass, $paramKey, $serviceId);
        $service = $this->getDiUtils()->getDiXmlServiceTag($serviceId, $newXml);
        $this->getDiUtils()->addEmArgTo($service);
        $this->getDiUtils()->addClassArgTo(
            $service,
            $this->getBundle()->getNamespace(),
            $this->getEntityNamespace(),
            $this->getEntity()
        );

        return $this->formatOutput(($newXml->asXML()) ?: '');
    }
}