<?php

namespace Tdn\ForgeBundle\Traits;

/**
 * Interface OverWritableInterface
 * @package Tdn\ForgeBundle\Traits
 */
interface OverWritableInterface
{
    /**
     * @param bool $overWrite
     */
    public function setOverWrite($overWrite);

    /**
     * @return bool
     */
    public function shouldOverWrite();
}
