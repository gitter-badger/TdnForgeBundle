<?php

namespace Tdn\ForgeBundle\Generator;

use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\File;

/**
 * Class FormGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class FormGenerator extends AbstractGenerator
{
    /**
     * Sets up a FormType based on entity.
     * Sets up InvalidFormException if it doesn't exist exists.
     * Adds Manager Dependency
     */
    protected function configure()
    {
        $this->addFormType();
        $this->addFormException();
        $this->addManagerDependency();

        parent::configure();
    }

    /**
     * @return void
     */
    protected function addFormType()
    {
        $typeName = $this->getEntity() . 'Type';
        $fileName = sprintf(
            '%s' . DIRECTORY_SEPARATOR .
            'Form' . DIRECTORY_SEPARATOR .
            'Type' . DIRECTORY_SEPARATOR . '%s.php',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
            $typeName
        );

        $formType = new File(
            $fileName,
            $this->getFormTypeContent($typeName)
        );

        $this->addFile($formType);
    }

    /**
     * @return void
     */
    protected function addFormException()
    {
        $invalidFormException = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR .
                'Exception' . DIRECTORY_SEPARATOR . 'InvalidFormException.php',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
            ),
            $this->getInvalidFormExceptionContent(),
            File::QUEUE_IF_UPGRADE
        );

        $this->addFile($invalidFormException);
    }

    /**
     * @param  string $fileName
     * @return string
     */
    protected function getFormTypeContent($fileName)
    {
        return $this->getTemplateStrategy()->render(
            'form/FormType.php.twig',
            [
                'fields'                => $this->getFieldsFromMetadata($this->getMetadata()),
                'associated'            => $this->getMetadata()->associationMappings,
                'namespace'             => $this->getBundle()->getNamespace(),
                'entity_namespace'      => $this->getEntityNamespace(),
                'entity_class'          => $this->getEntity(),
                'format'                 => $this->getFormat(),
                'bundle'                => $this->getBundle()->getName(),
                'entity_identifier'     => $this->getEntityIdentifier(),
                'form_class'            => String::create($fileName)->underscored()->toLowerCase(),
            ]
        );
    }

    /**
     * @return string
     */
    protected function getInvalidFormExceptionContent()
    {
        return $this->getTemplateStrategy()->render(
            'exception/InvalidFormException.php.twig',
            [
                'namespace' => $this->getBundle()->getNamespace()
            ]
        );
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
}
