<?php

namespace Mslib\App;

use Mslib\Container\Container;
use Mslib\Exception\MsException;
use Mslib\Router\Router;
use Mslib\View\View;
use Zend\Http\Response;

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
     */
    public function init($configFile)
    {
        try {
            // we first set the container
            $this->container = new Container();
            $this->container->init($configFile);

            // we then set the router
            $this->setRouter();

            // we resolve the request
            $response = $this->router->resolveRequest();

            $this->returnResponse($response);
        } catch (MsException $msException) {
            $this->returnErrorResponse($msException->getGeneralMessage());
        }
    }

    /**
     * Sets the Router for this app instance
     */
    protected function setRouter()
    {
        try {
            $this->router = new Router($this->container);
            $this->router->init('./app/config/routing.json', $this->container->getLogger());
        } catch (MsException $msException) {
            $this->returnErrorResponse($msException->getGeneralMessage());
        }
    }

    /**
     * Returns a general error JSON response to the client.
     *
     * @param string $message additional error message
     */
    protected function returnErrorResponse($message)
    {
        // Setting the response content from the view
        $view = new View("response.json.php");
        $content = $view->render(array(
            "status"    => "error",
            "code"      => "-1",
            "message"   => "An internal error occurred. Please contact the API owner. General error message: '$message'",
            "data"      => array()
        ));

        // Setting the headers
        header('Content-Type: application/json');
        header('Status: 500');

        // Returning the response
        echo $content;
    }

    /**
     * Returns a general JSON response to the client.
     *
     * @param Response $response
     */
    protected function returnResponse(Response $response)
    {
        header('Content-Type: application/json');
        header('Status: ' . $response->getStatusCode());
        echo $response->getContent();
    }
}