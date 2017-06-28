<?php

namespace Mslib\Exception;

/**
 * Class ConfigException: custom exception used for a general configuration error
 *
 * @codeCoverageIgnore
 *
 * @package Mslib\Exception
 */
class ConfigException extends MsException
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    public function getGeneralMessage()
    {
        return "API Configuration Error";
    }
}