<?php

namespace Tdn\ForgeBundle\Model;

/**
 * Class RouteDefinition
 * @package Tdn\ForgeBundle\Model
 */
class RouteDefinition
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $id
     * @param string $resource
     * @param string $prefix
     * @param string $type
     */
    public function __construct(
        $id,
        $resource,
        $prefix,
        $type
    ) {
        $this->id = $id;
        $this->resource = $resource;
        $this->prefix = $prefix;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
