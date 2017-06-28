<?php

namespace Mslib\Exception;

/**
 * Class RoutingException: custom exception used for a general routing error
 *
 * @codeCoverageIgnore
 *
 * @package Mslib\Exception
 */
class RoutingException extends MsException
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    public function getGeneralMessage()
    {
        return "Routing Error";
    }
}