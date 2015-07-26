<?php

namespace Tdn\ForgeBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\File;

/**
 * Class ControllerGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class ControllerGenerator extends AbstractGenerator
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var bool
     */
    private $swagger;

    /**
     * @var bool
     */
    private $tests;

    /**
     * @var bool
     */
    private $fixtures;

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
        return ($this->prefix) ? $this->prefix : '';
    }

    /**
     * @param bool $swagger
     */
    public function setSwagger($swagger)
    {
        $this->swagger = $swagger;
    }

    /**
     * @return bool
     */
    public function hasSwagger()
    {
        return $this->swagger;
    }

    /**
     * @param bool $tests
     */
    public function setTests($tests)
    {
        $this->tests = $tests;
    }

    /**
     * @return bool
     */
    public function supportsTests()
    {
        return $this->tests;
    }

    /**
     * @param bool $fixtures
     */
    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
    }

    /**
     * @return bool
     */
    public function supportsFixtures()
    {
        return $this->fixtures;
    }

    /**
     * Sets up a controller based on an entity.
     * Sets up controller test files if flag is set.
     * @return $this
     */
    public function configure()
    {
        $this->addHandlerDependency();
        $this->addController();

        return $this;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'prefix' => '',
            'swagger' => false,
            'tests' => false,
            'fixtures' => false
        ]);

        $resolver
            //Route Prefix
            ->setAllowedTypes('prefix', 'string')
            //Swagger
            ->setRequired('swagger')
            ->setAllowedTypes('swagger', 'bool')
            //Tests
            ->setRequired('tests')
            ->setAllowedTypes('tests', 'bool')
            //Fixtures
            ->setRequired('fixtures')
            ->setAllowedTypes('fixtures', 'bool')
        ;
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
        $this->setPrefix($options['prefix']);
        $this->setSwagger($options['swagger']);
        $this->setTests($options['tests']);
        $this->setFixtures($options['fixtures']);

        return $options;
    }

    /**
     * @return void
     */
    protected function addController()
    {
        $generatedController = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '%sController.php',
                ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath()),
                $this->getEntity()
            ),
            $this->getControllerContent()
        );

        $this->addFile($generatedController);
    }

    /**
     * @return string The controller contents
     */
    protected function getControllerContent()
    {
        $idType = $this->getIdentifierType($this->getMetadata());

        return $this->getTemplateStrategy()->render(
            'controller/controller.php.twig',
            [
                'entity_identifier_type' => $idType,
                'entity_identifier'      => $this->getEntityIdentifier(),
                'requirement_regex'      => $this->getRequirementRegex($idType),
                'route_prefix'           => $this->getPrefix(),
                'bundle'                 => $this->getBundle()->getName(),
                'entity'                 => $this->getEntity(),
                'entity_namespace'       => $this->getEntityNamespace(),
                'namespace'              => $this->getBundle()->getNamespace(),
                'swagger'                => $this->hasSwagger(),
                'format'                 => $this->getFormat(),
                'form_type'              => $this->getBundle()->getNamespace() .
                    '\\Form\\Type\\' . $this->getEntity() . 'Type',
                'entity_form_type'       => (string) String::create($this->getEntity())
                    ->underscored()
                    ->toLowerCase()
            ]
        );
    }

    /**
     * @param ClassMetadata $metadata
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getIdentifierType(ClassMetadata $metadata)
    {
        if (count($metadata->identifier) !== 1) {
            throw new \InvalidArgumentException(
                'This bundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        $identifier = array_values($metadata->identifier)[0];
        foreach ($metadata->fieldMappings as $field) {
            if ($field['fieldName'] == $identifier) {
                return $field['type'];
            }
        }

        return null;
    }

    /**
     * @param string $idType
     * @return string
     */
    protected function getRequirementRegex($idType)
    {
        switch ($idType) {
            case 'string':
                return '\w+';
            case 'int':
            case 'integer':
                return '\d+';
            default:
                return '';
        }
    }

    /**
     * @return void
     */
    protected function addHandlerDependency()
    {
        $handlerFile = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR . '%sHandler.php',
            ($this->getTargetDirectory()) ?: realpath($this->getBundle()->getPath()),
            $this->getEntity()
        );

        $this->addFileDependency(new File($handlerFile));
    }
}
