<?php

namespace Tdn\ForgeBundle\Traits;

/**
 * Trait OverWritable
 * @package Tdn\ForgeBundle\Traits
 */
trait OverWritable
{
    /**
     * @var bool
     */
    private $overWrite;

    /**
     * @param bool $overWrite
     */
    protected function setOverWrite($overWrite)
    {
        $this->overWrite = $overWrite;
    }

    /**
     * @return bool
     */
    public function shouldOverWrite()
    {
        return $this->overWrite;
    }
}
