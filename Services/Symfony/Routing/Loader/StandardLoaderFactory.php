<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Loader;

use Symfony\Component\Config\FileLocator;
use Tuck\ConverterBundle\Exception\UnknownFormatException;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\LoaderInterface;

/**
 * Class StandardLoaderFactory
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Loader
 */
class StandardLoaderFactory implements LoaderFactoryInterface
{
    protected $loaderMap = [
        Format::XML  => '\Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\XmlLoader',
        Format::YAML => '\Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver\YamlLoader'
    ];

    /**
     * @param string $type
     * @param string $path
     *
     * @return LoaderInterface
     *
     * @throws UnknownFormatException
     */
    public function createLoader($type, $path)
    {
        $class = $this->getClassFromType($type);

        return new $class(new FileLocator($path));
    }

    /**
     * @param string $type
     *
     * @return string
     *
     * @throws UnknownFormatException
     */
    protected function getClassFromType($type)
    {
        if (!isset($this->loaderMap[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->loaderMap[$type];
    }
}
