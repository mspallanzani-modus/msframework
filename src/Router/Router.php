<?php

namespace Mslib\Router;

use Mslib\Exception\ConfigException;
use Mslib\Exception\RoutingException;
use Psr\Log\LoggerInterface;
use Zend\Config\Exception\RuntimeException;
use Zend\Config\Reader\Json;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class Router: it implements a routing system for the application.
 *
 * @package Mslib\Router
 */
class Router
{
    /**
     * @var LoggerInterface The logger instance
     */
    protected $logger;

    /**
     * @var array List of all supported routes
     */
    protected $routes = array();

    /**
     * Initializes all supported routes from the given configuration file (JSON supported)
     *
     * @param $configFile JSON routing configuration file
     * @param LoggerInterface $logger The logger instance
     *
     * @throws ConfigException
     */
    public function init($configFile, LoggerInterface $logger)
    {
        // setting the logger first
        $this->logger = $logger;

        // reading the config: json file expected
        try {
            $reader = new Json();
            $data   = $reader->fromFile($configFile);

            // we check if routes are defined
            if (array_key_exists("routes", $data)) {
                foreach ($data['routes'] as $routeName => $routeParams) {
                    // creating a new route
                    $route = new Route();
                    $route->setName($routeName);

                    // checking if url param exists
                    if (array_key_exists('url', $routeParams)) {
                        $route->setUrl($routeParams['url']);
                    } else {
                        // missing url param: we skip this route
                        $logger->warning(
                            "The route '$routeName' does not define the 'url' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if http-method param exists
                    if (array_key_exists('http-method', $routeParams)) {
                        $route->setHttpMethod($routeParams['http-method']);
                    } else {
                        // missing http-method param: we skip this route
                        $logger->warning(
                            "The route '$routeName' does not define the 'http-method' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if controller param exists
                    if (array_key_exists('controller', $routeParams)) {
                        $route->setController($routeParams['controller']);
                    } else {
                        // missing controller param: we skip this route
                        $logger->warning(
                            "The route '$routeName' does not define the 'controller' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if controller method param exists
                    if (array_key_exists('method', $routeParams)) {
                        $route->setMethod($routeParams['method']);
                    } else {
                        // missing method param: we skip this route
                        $logger->warning(
                            "The route '$routeName' does not define the 'method' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // route should be now fully configured: we can add it to the collection of valid routes
                    $this->routes[] = $route;
                }
            } else {
                throw new ConfigException("No routes defined.");
            }
        } catch (RuntimeException $e) {
            throw new ConfigException(
                "It was not possible to read the given configuration file '$configFile'. " .
                "Check that it is accessible and/or it is a valid json file.");
        }
    }

    /**
     * Returns an instance of Mslib\Router\Route mapped to the current HTTP request.
     * If no route is found, a RoutingException will be thrown.
     *
     * @return Route
     *
     * @throws RoutingException
     */
    public function resolveRequest()
    {
        // creating a request object from the php environment
        $request = new Request();

        // logging the request that will be treated
        $this->logger->info(sprintf(
            "processing the following request: '%s' - HTTP METHOD: '%s'",
            $request->getUriString(),
            $request->getMethod()
        ));

        // We look for a route associated to the current HTTP request
        $route = $this->matchRequest($request);
        if (!($route instanceof Route)) {
            throw new RoutingException("The requested request is not supported");
        }

        /** @var $route Route */
        // We found a valid Route object associated to the current HTTP request: we call the Controller method
        $controllerClass = $route->getController();
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass($this->logger);
            $method = $route->getMethod();
            if (method_exists($controller, $method)) {
                // we call the controller method
                $return = call_user_func(array($controller, $method), $request);
                var_dump($return);
            } else {
                $this->logger->error("The method '$method' is not defined in the class '$controllerClass'.");
                throw new RoutingException("Invalid method for the matched route");
            }
        } else {
            throw new RoutingException(
                "The controller class '$controllerClass' for the route '$route->getName()' does not exist"
            );
        }
        return $route;
    }

    /**
     * Returns a Route object that matches the given HTTP Request object; if none found, null will be returned.
     *
     * @param Request $request The HTTP Request object representing the current http request
     *
     * @return Route|null
     */
    protected function matchRequest(Request $request)
    {
        foreach ($this->routes as $route) {
            /** @var $route Route */
            if ($route->getUrl() === $request->getRequestUri() &&
                    $route->getHttpMethod() === $request->getMethod()) {
                // The current route and the given request have the same uri and the same http method: it's a match!
                return $route;
            }
        }
        return null;
    }
}