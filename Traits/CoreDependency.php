<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Exception\CoreDependencyMissingException;

/**
 * Class CoreDependency
 * @package Tdn\ForgeBundle\Traits
 */
trait CoreDependency
{
    /**
     * @param string $message
     *
     * @return CoreDependencyMissingException
     */
    protected function createCoreDependencyMissingException($message)
    {
        return new CoreDependencyMissingException($message);
    }
}
