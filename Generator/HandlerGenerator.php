<?php

namespace Tdn\ForgeBundle\Generator;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\ServiceDefinition;

/**
 * Class HandlerGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class HandlerGenerator extends AbstractServiceGenerator
{
    /**
     * Generates REST Handler based on entity.
     *
     * @return $this
     */
    public function configure()
    {
        $handler = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getEntity()
            ),
            $this->getHandlerFileContent()
        );

        $this->addFile($handler);
        $this->addManagerDependency();
        $this->addFormTypeDependency();

        if ($this->getFormat() !== Format::ANNOTATION) {
            $this->addHandlerServiceFile();
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getHandlerFileContent()
    {
        return $this->getTemplateStrategy()->render('handler/handler.php.twig', [
            'entity'            => $this->getEntity(),
            'entity_identifier' => $this->getEntityIdentifier(),
            'format'            => $this->getFormat(),
            'namespace'         => $this->getBundle()->getNamespace(),
            'service_id'        => $this->getServiceId()
        ]);
    }

    /**
     * @return void
     */
    protected function addHandlerServiceFile()
    {
        $filePath = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Resources' .
            DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'handlers.%s',
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
     * Declares service and returns what the contents would be based on the format selected
     *
     * @param string $filePath
     *
     * @return string
     */
    public function getServiceFileContents($filePath)
    {
        $serviceClass = sprintf(
            '%s\\Handler\\%sHandler',
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
            ->addArgument(new Reference('form.factory'))
        ;

        return $this->getServiceManager()
            ->addParameter($paramKey, $serviceClass)
            ->addServiceDefinition(new ServiceDefinition($serviceId, $definition))
            ->dump($filePath)
        ;
    }

    /**
     * @return void
     */
    protected function addManagerDependency()
    {
        $managerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Entity' . DIRECTORY_SEPARATOR .
            'Manager' . DIRECTORY_SEPARATOR . '%sManager.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new File($managerFile));
    }

    /**
     * @return void
     */
    protected function addFormTypeDependency()
    {
        $formType = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Form' . DIRECTORY_SEPARATOR . 'Type' . DIRECTORY_SEPARATOR . '%sType.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $this->getEntity()
        );

        $this->addFileDependency(new File($formType));
    }

    /**
     * @return string
     */
    private function getServiceId()
    {
        return sprintf(
            '%s.handler.%s_handler',
            $this->getServiceNamespace(),
            $this->getServiceEntityName()
        );
    }

    private function getServiceClass()
    {
        return $this->getServiceId() . '.class';
    }
}
