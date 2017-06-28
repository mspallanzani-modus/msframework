<?php

namespace Mslib\Exception;

/**
 * Class RenderException: custom exception used for a general rendering error
 *
 * @codeCoverageIgnore
 *
 * @package Mslib\Exception
 */
class RenderException extends MsException
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    public function getGeneralMessage()
    {
        return "Render Error";
    }
}