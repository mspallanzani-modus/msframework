<?php

namespace Mslib\Exception;

/**
 * Class EntityNotFoundException: custom exception used for not found entity
 *
 * @codeCoverageIgnore
 *
 * @package Mslib\Exception
 */
class EntityNotFoundException extends MsException
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    public function getGeneralMessage()
    {
        return "Entity Not Found";
    }
}