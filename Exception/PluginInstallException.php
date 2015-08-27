<?php

namespace Tdn\ForgeBundle\Exception;

/**
 * Class PluginInstallException
 * @package Tdn\ForgeBundle\Exception
 */
class PluginInstallException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = 'Error installing plugin ' . $message;
        return parent::__construct($message, $code, $previous);
    }
}
