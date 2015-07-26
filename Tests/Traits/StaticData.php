<?php

namespace Tdn\ForgeBundle\Tests\Traits;

/**
 * Trait StaticData
 * @package Tdn\ForgeBundle\Tests\Traits
 */
trait StaticData
{
    /**
     * @param string $directory
     * @param string $file

     * @return string
     */
    protected static function getStaticData($directory, $file)
    {
        $path = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'static' .
            DIRECTORY_SEPARATOR . $directory
        );

        $path = $path . DIRECTORY_SEPARATOR . $file;

        return file_get_contents($path);
    }
}
