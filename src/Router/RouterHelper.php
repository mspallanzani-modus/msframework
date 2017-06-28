<?php

namespace Mslib\Router;
use Mslib\Container\Container;
use Mslib\Controller\MsController;
use Mslib\Exception\InitException;
use Mslib\Repository\MsRepository;

/**
 * Class RouterHelper: methods to execute the routing actions
 *
 * @package Mslib\Router
 */
class RouterHelper
{
    /**
     * Returns a MsRepository child instance for the given Controller class
     *
     * @param string $controllerClass The namespace of the controller class
     * @param Container $container The service container
     *
     * @return MsRepository
     *
     * @throws InitException
     */
    public static function getRepository($controllerClass, Container $container)
    {
        $repositoryClass = str_replace("Controller", "Repository", $controllerClass);
        if (class_exists($repositoryClass) && is_subclass_of($repositoryClass, MsRepository::class)) {
            return new $repositoryClass($container->getDbConnection());
        } else {
            throw new InitException(
                "It was not possible to find a valid Repository class for the given Controller class '$controllerClass'. " .
                "Please check if you have implemented a class 'Mslib\\Repository\\" . $repositoryClass . "Repository " .
                "that extends the 'Mslib\\Repository\\MsRepository' class"
            );
        }
    }

    /**
     * Returns a MsController child instance for the give Controller class
     *
     * @param string $controllerClass The namespace of the controller class
     * @param Container $container The service container
     *
     * @return MsController
     *
     * @throws InitException
     */
    public static function getController($controllerClass, Container $container)
    {
        if (class_exists($controllerClass) && is_subclass_of($controllerClass, MsController::class)) {
            return new $controllerClass($container->getLogger(), RouterHelper::getRepository($controllerClass, $container), $container);
        } else {
            throw new InitException(
                "The given controller class '$controllerClass' does not exist " .
                "or does not extends the 'Mslib\\Controller\\MsController' class"
            );
        }
    }
}