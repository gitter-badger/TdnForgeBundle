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
        $doc = new \DOMDocument();
        if (!$doc->load($path)) {
            throw new InvalidXmlException("Error parsing file.");
        }

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
            /** @var \DOMElement $default */
            $defaults = [];
            foreach ($import->getElementsByTagName('default') as $default) {
                $defaults[$default->getAttribute('key')] = $default->nodeValue;
            }

            $routeDefinition = new RouteDefinition(
                $import->getAttribute('id'),
                $import->getAttribute('resource'),
                $import->getAttribute('prefix'),
                $import->getAttribute('type'),
                $defaults
            );

            $routeCollection->set($routeDefinition->getId(), $routeDefinition);
        }

        return $routeCollection;
    }
}
