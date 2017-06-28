<?php

namespace Mslib\App;

use Mslib\Container\Container;
use Mslib\Exception\MsException;
use Mslib\Router\Router;
use Mslib\View\View;
use Mslib\View\ViewHelper;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class App: main application class.
 *
 * @package Mslib\App
 */
class App
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Initializes all required services (Logger, DB connection, ect).
     *
     * @param string $configFile The configuration file path
     * @param string $routingConfigFile The routing configuration file path
     *
     * @return null|Response
     *
     * @codeCoverageIgnore
     */
    public function init($configFile, $routingConfigFile)
    {
        try {
            // we first set the container
            $this->container = new Container();
            $this->container->init($configFile);

            // we then set the router
            $this->router = new Router($this->container);
            $this->router->init($routingConfigFile, $this->container->getLogger());
        } catch (MsException $msException) {
            return $this->returnErrorResponse($msException->getGeneralMessage());
        }
    }

    /**
     * Resolve an HTTP request.
     *
     * @return Response
     *
     * @codeCoverageIgnore
     */
    public function resolveRequest()
    {
        try {
            // creating a request object from the php environment
            $request = new Request();

            // we resolve the request
            return $this->router->resolveRequest($request);
        } catch (MsException $msException) {
            $this->returnErrorResponse($msException->getGeneralMessage());
        }
    }

    /**
     * Sets a Container instance for this App instance
     *
     * @param Container $container The container to be set
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Sends a response back to the client for the given Response object
     *
     * @param Response $response
     *
     * @codeCoverageIgnore
     *
     */
    public function sendResponse(Response $response)
    {
        // Simply send the response back
        $response->send();
    }

    /**
     * Returns a general ERROR JSON response to the client for the given Response object.
     *
     * @param string $message Additional error message
     *
     * @return Response
     */
    public function returnErrorResponse($message)
    {
        // Creating the response content from the view
        $view = ViewHelper::getViewForTemplate($this->container,"response.json.php");
        $content = $view->render(array(
            "status"    => "error",
            "code"      => "-1",
            "message"   => "An internal error occurred. Please contact the API owner. General error message: '$message'",
            "data"      => array()
        ));

        // Creating and returning the response object
        $response = new Response();
        $response->setStatusCode(500);
        $response->getHeaders()->addHeaderLine('Content-Type: application/json');
        $response->setContent($content);
        return $response;
    }
}