<?php

namespace Tdn\ForgeBundle\Traits;

/**
 * Trait Formattable
 * @package Tdn\ForgeBundle\Traits
 */
trait Formattable
{
    /**
     * @var string
     */
    private $format;

    /**
     * @return array
     */
    abstract public function getSupportedFormats();

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    protected function setFormat($format)
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            throw new \InvalidArgumentException('Invalid format ' . $format);
        }

        $this->format = $format;
    }
}
