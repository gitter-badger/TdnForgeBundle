<?php

namespace Tdn\ForgeBundle\Traits;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Interface DoctrineMetadataInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface DoctrineMetadataInterface
{
    /**
     * @param ClassMetadata $metadata
     */
    public function setMetadata(ClassMetadata $metadata);

    /**
     * @return ClassMetadata
     */
    public function getMetadata();

    /**
     * @return string
     */
    public function getEntity();

    /**
     * @return string
     */
    public function getEntityNamespace();
}
