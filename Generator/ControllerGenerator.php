<?php

namespace Tdn\ForgeBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tdn\ForgeBundle\Generator\Plugin\Controller\TestPlugin;
use Tdn\PhpTypes\Type\String;
use Tdn\ForgeBundle\Model\File;

/**
 * Class ControllerGenerator
 * @package Tdn\ForgeBundle\Generator
 */
class ControllerGenerator extends AbstractGenerator
{
    /**
     * @var bool
     */
    private $swagger;

    /**
     * @var bool
     */
    private $tests;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $fixturesPath;

    /**
     * Sets up a controller based on an entity.
     * Sets up controller test files if flag is set.
     *
     * @return $this
     */
    protected function configure()
    {
        $this->addHandlerDependency();
        $this->addController();
        if ($this->supportsTests()) {
        }

        parent::configure();
    }

    /**
     * @param bool $swagger
     */
    protected function setSwagger($swagger)
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
    protected function setTests($tests)
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
        return ($this->prefix) ? $this->prefix : '';
    }

    /**
     * @param string $fixturesPath
     */
    protected function setFixturesPath($fixturesPath)
    {
        $this->fixturesPath = $fixturesPath;
    }

    /**
     * @return string
     */
    public function getFixturesPath()
    {
        return $this->fixturesPath;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'prefix' => '',
            'fixtures-path' => '',
            'swagger' => false,
            'tests' => false
        ]);

        $resolver
            ->setDefined('prefix')
            ->setAllowedTypes('prefix', 'string')

            ->setDefined('fixtures-path')
            ->setAllowedTypes('fixtures-path', 'string')

            ->setDefined('swagger')
            ->setAllowedTypes('swagger', 'bool')

            ->setDefined('tests')
            ->setAllowedTypes('tests', 'bool')
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
        $this->setSwagger($options['swagger']);
        $this->setTests($options['tests']);
        $this->setPrefix($options['prefix']);

        if ($this->supportsTests()) {
            $this->setFixturesPath($options['fixtures-path']);
        }

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
        return $this->getTemplateStrategy()->render(
            'controller/controller.php.twig',
            [
                'entity_identifier_type' => $this->getIdentifierType(),
                'entity_identifier'      => $this->getEntityIdentifier(),
                'requirement_regex'      => $this->getRequirementRegex($this->getIdentifierType()),
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
     * @return string
     */
    protected function getIdentifierType()
    {
        $identifier = array_values($this->getMetadata()->identifier)[0];

        foreach ($this->getMetadata()->fieldMappings as $field) {
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

    protected function isValid()
    {
        if (!class_exists('\FOS\RestBundle\FOSRestBundle') && !$this->shouldForceGeneration()) {
            $this->createCoreDependencyMissingException('Please install FOSRestBundle.');
        }

        if (!class_exists('JMS\SerializerBundle\JMSSerializerBundle') && !$this->shouldForceGeneration()) {
            $this->createCoreDependencyMissingException('Please install JMSSerializerBundle.');
        }

        return parent::isValid();
    }
}
