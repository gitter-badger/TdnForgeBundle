<?php

namespace Tdn\ForgeBundle\Traits;

interface FormattableInterface
{
    /**
     * @return array
     */
    public function getSupportedFormats();

    /**
     * @return string
     */
    public function getFormat();
}
