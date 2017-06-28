<?php

namespace Mslib\Exception;

/**
 * Class InitException: custom exception used for a general initialization error
 *
 * @codeCoverageIgnore
 *
 * @package Mslib\Exception
 */
class InitException extends MsException
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    public function getGeneralMessage()
    {
        return "Initialization Error";
    }
}