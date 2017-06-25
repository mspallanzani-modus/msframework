<?php

namespace Mslib\Controller;

use Psr\Log\LoggerInterface;

/**
 * Class MsController: General Controller class.
 *
 * @package Mslib\Controller
 */
abstract class MsController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MsController constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
//TODO this injection could be replace by a proper service container
        $this->logger = $logger;
    }
}