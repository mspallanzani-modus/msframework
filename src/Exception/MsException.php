<?php

namespace Mslib\Exception;

/**
 * Class MsException: general framework exception
 *
 * @package Mslib\Exception
 */
abstract class MsException extends \Exception
{
    /**
     * Returns a general error message for this exception
     *
     * @return string
     */
    abstract public function getGeneralMessage();
}