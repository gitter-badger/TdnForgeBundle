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
     * @return ClassMetadata
     */
    public function getMetadata();
}
