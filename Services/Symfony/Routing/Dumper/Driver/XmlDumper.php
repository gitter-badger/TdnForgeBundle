<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver;

use Tdn\ForgeBundle\Model\RouteDefinition;

/**
 * Class XmlDumper
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver
 */
class XmlDumper extends AbstractDumper implements DumperInterface
{
    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * Dumps the xml string representation of a Route Collection.
     *
     * @return string
     */
    public function dump()
    {
        $this->document = new \DOMDocument('1.0', 'utf-8');
        $routesDoc = $this->document->createElementNS('http://friendsofsymfony.github.com/schema/rest', 'routes');
        $routesDoc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $routesDoc->setAttribute(
            'xsi:schemaLocation',
            'http://friendsofsymfony.github.com/schema/rest ' .
            'http://friendsofsymfony.github.com/schema/rest/routing-1.0.xsd'
        );

        foreach ($this->routeCollection as $route) {
            $this->addRoute($route, $routesDoc);
        }

        $this->document->appendChild($routesDoc);
        $this->document->formatOutput = true;
        $this->document->preserveWhiteSpace = true;

        return $this->document->saveXML();
    }

    /**
     * @param RouteDefinition $route
     * @param \DOMElement $parent
     */
    protected function addRoute(RouteDefinition $route, \DOMElement $parent)
    {
        $importEntry = $this->document->createElement('import');
        $importEntry->setAttribute('id', $route->getId());
        $importEntry->setAttribute('resource', $route->getResource());
        $importEntry->setAttribute('type', $route->getType());
        if ($route->getPrefix()) {
            $importEntry->setAttribute('prefix', $route->getPrefix());
        }
        $parent->appendChild($importEntry);
    }
}
