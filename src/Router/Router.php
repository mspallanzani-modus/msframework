<?php

namespace Mslib\Router;

use Mslib\Container\App;
use Mslib\Container\Container;
use Mslib\Exception\ConfigException;
use Mslib\Exception\InitException;
use Mslib\Exception\RoutingException;
use Zend\Config\Exception\RuntimeException;
use Zend\Config\Reader\Json;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class Router: it implements a routing system for the application.
 *
 * @package Mslib\Router
 */
class Router
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array List of all supported routes
     */
    protected $routes = array();

    /**
     * Router constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Initializes all supported routes from the given configuration file (JSON supported)
     *
     * @param $routerConfigFile JSON routing configuration file
     *
     * @throws ConfigException
     */
    public function init($routerConfigFile)
    {
        // reading the router config: json file expected
        try {
            $reader = new Json();
            $data   = $reader->fromFile($routerConfigFile);

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
                        $this->container->getLogger()->warning(
                            "The route '$routeName' does not define the 'url' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if http-method param exists
                    if (array_key_exists('http-method', $routeParams)) {
                        $route->setHttpMethod($routeParams['http-method']);
                    } else {
                        // missing http-method param: we skip this route
                        $this->container->getLogger()->warning(
                            "The route '$routeName' does not define the 'http-method' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if controller param exists
                    if (array_key_exists('controller', $routeParams)) {
                        $route->setController($routeParams['controller']);
                    } else {
                        // missing controller param: we skip this route
                        $this->container->getLogger()->warning(
                            "The route '$routeName' does not define the 'controller' parameter. This route will be ignored."
                        );
                        continue;
                    }

                    // checking if controller method param exists
                    if (array_key_exists('method', $routeParams)) {
                        $route->setMethod($routeParams['method']);
                    } else {
                        // missing method param: we skip this route
                        $this->container->getLogger()->warning(
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
                "It was not possible to read the given configuration file '$routerConfigFile'. " .
                "Check that it is accessible and/or it is a valid json file.");
        }
    }

    /**
     * Returns an instance of Mslib\Router\Route mapped to the current HTTP request.
     * If no route is found, a RoutingException will be thrown.
     *
     * @param Request $request The Request object to be treated
     *
     * @return Response
     *
     * @throws RoutingException
     */
    public function resolveRequest(Request $request)
    {
        // logging the request that will be treated
        $this->container->getLogger()->info(sprintf(
            "processing the following request: '%s' - HTTP METHOD: '%s'",
            $request->getUriString(),
            $request->getMethod()
        ));

        // We look for a route associated to the current HTTP request
        $route = $this->matchRequest($request);
        if (!($route instanceof Route)) {
            throw new RoutingException("The requested url is not supported");
        }

        /** @var $route Route */
        // We found a valid Route object associated to the current HTTP request: we call the Controller method
        $controllerClass = $route->getController();
        try {
            // we get the controller 
            $controller = RouterHelper::getController($controllerClass, $this->container);
            
            // we check if the controller method exists
            $method = $route->getMethod();
            if (method_exists($controller, $method)) {
                // we call the controller method
                return call_user_func(array($controller, $method), $request);
            } else {
                $message = "The method '$method' is not defined in the class '$controllerClass'.";
                $this->container->getLogger()->error($message);
                throw new RoutingException("Invalid method for the matched route. Error message: $message");
            }
        } catch (InitException $initEx) {
            throw new RoutingException(
                "It was not possible to initialize a controller class of type '$controllerClass' " . 
                "for the route '". $route->getName() . "' . Error message is: " .$initEx->getMessage()
            );
        }
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
//TODO this should be done with a regular expression
            // We remove all get parameters
            $requestUri = $request->getRequestUri();
            if (strpos($requestUri, "?") > 0) {
                $requestUri = substr($request->getRequestUri(), 0, strpos($requestUri, "?"));
            }

            /** @var $route Route */
            if ($route->getUrl() === $requestUri &&
                    $route->getHttpMethod() === $request->getMethod()) {
                // The current route and the given request have the same uri and the same http method: it's a match!
                return $route;
            }
        }
        return null;
    }
}