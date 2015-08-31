<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Exception\InvalidXmlException;
use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class XmlLoader
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Loader\Driver
 */
class XmlLoader extends AbstractLoader implements LoaderInterface
{
    /**
     * @param $resource
     * @return ArrayCollection|RouteDefinition[]
     */
    public function load($resource)
    {
        $path = $this->locator->locate($resource);
        $content = $this->loadFile($path);

        return $this->loadRoutes($content);
    }

    /**
     * @param $path
     * @throws InvalidXmlException
     *
     * @return \DOMDocument
     */
    protected function loadFile($path)
    {
        $prev = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();

        if (false === $doc->load($path)) {
            $errors = new ArrayCollection(libxml_get_errors());
            $errors = $errors->map(function ($error) {
                if (is_string($error)) {
                    return $error;
                }

                if ($error instanceof \libXmlError) {
                    return $error->message;
                }
            });

            throw new InvalidXmlException(
                sprintf(
                    'Error(s) parsing file: %s.',
                    implode(', ', $errors->toArray())
                )
            );
        }

        libxml_use_internal_errors($prev);

        return $doc;
    }

    /**
     * @param \DOMDocument $doc
     * @return ArrayCollection
     */
    protected function loadRoutes(\DOMDocument $doc)
    {
        $routeCollection = new ArrayCollection();
        /** @var \DOMElement $import */
        foreach ($doc->getElementsByTagName('import') as $import) {
            $routeDefinition = new RouteDefinition(
                $import->getAttribute('id'),
                $import->getAttribute('resource'),
                ($import->hasAttribute('prefix') ? $import->getAttribute('prefix') : ''),
                $import->getAttribute('type')
            );

            $routeCollection->set($routeDefinition->getId(), $routeDefinition);
        }

        return $routeCollection;
    }
}
