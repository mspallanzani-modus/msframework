<?php

namespace Mslib\Test\Router;

use Mslib\Exception\ConfigException;
use Mslib\Exception\RoutingException;
use Mslib\Router\Router;
use Mslib\Test\MsTestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\Parameters;

/**
 * Class RouterTest: all Router tests
 *
 * @package Mslib\Test\Router
 */
class RouterTest extends MsTestCase
{
    /**
     * Returns an array containing all expected responses for the show action
     *
     * @return array
     */
    public function resolveRequestProvider()
    {
        return array(
            array("/user?id=1", 200, false, "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"\",    \"data\"      : {\"id\":\"1\",\"email\":\"test@testbox.dev\",\"firstname\":\"test\",\"lastname\":\"testtest\"}}"),
            array("/user-url?id=1", 400, true, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
            array("/user-http?id=1", 400, true, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
            array("/user-controller?id=1", 400, true, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
            array("/user-method?id=1", 400, true, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
        );
    }

    /**
     * Checks the init method: success and error cases
     */
    public function testInit()
    {
        // We initialize the router with a container with mocked PDO
        $mockContainer = $this->getContainerWitMockedPDO();
        $router = new Router($mockContainer);

        // We init with a wrong JSON file
        $routingFile = './src/Test/config/config.wrongformat.json';
        $this->expectException(ConfigException::class);
        $router->init($routingFile);

        // We init with a valid JSON file but routes are wrongly configured
        $routingFile = './src/Test/config/empty.routing.json';
        $this->expectException(ConfigException::class);
        $router->init($routingFile);
    }

    /**
     * Checks the resolveRequest method: success and error cases
     *
     * @dataProvider resolveRequestProvider
     */
    public function testResolveRequest($uri, $statusCode, $expectedExpection, $expectedResponse)
    {
        // We initialize the router with a container with mocked PDO
        $mockContainer = $this->getContainerWitMockedPDO(true);
        $router = new Router($mockContainer);

        // We init the router
        $router->init("./src/Test/config/routing.json");

        // Preparing the Request
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setRequestUri($uri);
        $request->setUri($uri);

        // We resolve the request for well configured route: response 200 expected
        if ($expectedExpection) {
            $this->expectException(RoutingException::class);
        }
        $response = $router->resolveRequest($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedResponse, $response->getContent());
    }
}


