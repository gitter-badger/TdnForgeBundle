<?php

namespace Tdn\ForgeBundle\Traits;

interface FormattableInterface
{
    /**
     * @return array
     */
    public function getSupportedFormats();

    /**
     * @param $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getFormat();
}
