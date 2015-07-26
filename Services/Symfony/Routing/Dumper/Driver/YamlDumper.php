<?php

namespace Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlDumper
 * @package Tdn\ForgeBundle\Services\Symfony\Routing\Dumper\Driver
 */
class YamlDumper extends AbstractDumper implements DumperInterface
{
    /**
     * Dumps the YAML representation of a Route Collection.
     *
     * @return string
     */
    public function dump()
    {
        $definitions = [];

        foreach ($this->routeCollection as $route) {
            $definitions[$route->getId()] = [
                'resource' => $route->getResource(),
                'type'     => $route->getType()
            ];

            if (!empty($route->getPrefix())) {
                $definitions[$route->getId()]['prefix'] = $route->getPrefix();
            }
        }

        return Yaml::dump($definitions);
    }
}
