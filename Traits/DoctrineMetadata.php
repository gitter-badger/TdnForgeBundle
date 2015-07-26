<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\PhpTypes\Type\String;

/**
 * Trait DoctrineMetadata
 * @package Tdn\ForgeBundle\Traits
 */
trait DoctrineMetadata
{
    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @param ClassMetadata $metadata
     */
    public function setMetadata(ClassMetadata $metadata)
    {
        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException(sprintf(
                'The %s does not support entity classes with multiple primary keys.',
                __CLASS__
            ));
        }

        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }


    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->cleanMetadataProperty($this->getMetadata()->getName());
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->cleanMetadataProperty($this->getMetadata()->namespace);
    }

    /**
     * Find entity identifier.
     *
     * Figures out what an entity's identifier is from it's metadata
     * And returns the name of the identifier.
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    protected function getEntityIdentifier()
    {
        if (count($this->getMetadata()->identifier) !== 1) {
            throw new \RuntimeException(
                'TdnForgeBundle is incompatible with entities that contain more than one identifier or no identifier.'
            );
        }

        return $this->getMetadata()->getIdentifierFieldNames()[0];
    }

    /**
     * Gets the entity's fields.
     *
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param  ClassMetadata $metadata
     *
     * @return array $fields
     */
    protected function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        $fields = $metadata->fieldMappings;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            foreach ($metadata->identifier as $identifier) {
                unset($fields[$identifier]);
            }
        }

        return array_merge($fields, $this->getAssociationMappings($metadata));
    }

    /**
     * Gets the short version of a Entity's FQDN
     *
     * Take an entity name and return the shortcut name
     * eg Acme\DemoBundle\Entity\Note -> AcmeDemoBundle:Note
     *
     * @param string $entity Fully qualified class name of the entity
     *
     * @return string
     */
    protected function getEntityBundleShortcut($entity)
    {
        $path = explode('\Entity\\', $entity);
        return str_replace('\\', '', $path[0]) . ':' . $path[1];
    }

    /**
     * Cleans properties with namespaces appended to them.
     *
     * This method always assumes that the directory for entities
     * will be `Entity` (symfony-standard). Will pop the last part of a string on
     * directory separators and assume it's the proper value.
     *
     * @param string $property
     *
     * @return string
     */
    private function cleanMetadataProperty($property)
    {
        $parts = explode('\\', $property);
        $realProperty = String::create(array_pop($parts));

        return ((string) $realProperty->toLowerCase() !== 'entity') ? (string) $realProperty : '';
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return array
     */
    private function getAssociationMappings(ClassMetadata $metadata)
    {
        $fields = $metadata->associationMappings;

        foreach ($fields as $fieldName => $relation) {
            $fields[$fieldName]['relatedEntityShortcut'] =
                $this->getEntityBundleShortcut($relation['targetEntity']);
        }

        return $fields;
    }
}
