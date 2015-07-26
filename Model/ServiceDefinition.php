<?php

namespace Tdn\ForgeBundle\Model;

use Symfony\Component\DependencyInjection\Definition;

/**
 * A service definition value object.
 *
 * Class ServiceDefinition
 * @package Tdn\ForgeBundle\Model
 */
class ServiceDefinition
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * @param $id
     * @param Definition $definition
     */
    public function __construct($id, Definition $definition)
    {
        $this->id = $id;
        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}
