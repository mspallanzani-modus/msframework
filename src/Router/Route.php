<?php

namespace Mslib\Router;

/**
 * Class Route: it represents a supported route. For each route, it is possible to define its relative url,
 * its HTTP method and the Controller class and method associated to it.
 *
 * @package Mslib\Router
 */
class Route
{
    /**
     * Route name (used in configuration)
     *
     * @var string
     */
    protected $name;

    /**
     * Route relative url (e.g. /user/create)
     *
     * @var string
     */
    protected $url;

    /**
     * Route HTTP method (e.g. GET, POST)
     *
     * @var string
     */
    protected $httpMethod;

    /**
     * Controller class for this Route (full namespace: Mslib\Controller\UserController)
     *
     * @var string
     */
    protected $controller;

    /**
     * Controller method associated to this Route (e.g. createUser)
     *
     * @var string
     */
    protected $method;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}