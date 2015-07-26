<?php

namespace Tdn\ForgeBundle\Traits;

use Tdn\ForgeBundle\Exception\OptionalDependencyMissingException;

/**
 * Class OptionalDependency
 * @package Tdn\ForgeBundle\Traits
 */
trait OptionalDependency
{
    /**
     * @param string $message
     *
     * @return OptionalDependencyMissingException
     */
    protected function createOptionalDependencyMissingException($message)
    {
        return new OptionalDependencyMissingException($message);
    }
}
