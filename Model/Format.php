<?php

namespace Tdn\ForgeBundle\Model;

/**
 * Interface Format
 *
 * This will be broken down into a factory of sorts.
 *
 * @package Tdn\ForgeBundle\Model
 */
interface Format
{
    const XML = 'xml';
    const YAML = 'yaml';
    const YML = 'yml';
    const ANNOTATION = 'annotation';

    public function __toString();
}
