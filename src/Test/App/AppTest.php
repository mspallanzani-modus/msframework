<?php

namespace Mslib\Test\App;

use Mslib\App\App;
use Mslib\Test\MsTestCase;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class AppTest: all App unit tests
 *
 * @package Mslib\Test\App
 */
class AppTest extends MsTestCase
{
    /**
     * It checks if a valid Error Response object is returned with the expected error message
     */
    public function testReturnErrorResponser()
    {
        // Mocking the container
        $mockContainer = $this->getContainerWitMockedPDO();

        // Creating the App
        $app = new App();
        $app->setContainer($mockContainer);

        // Getting the error response object
        $response = $app->returnErrorResponse("TEST ERROR");

        // The response object should be a Zend\Http\PhpEnvironment\Response object
        $this->assertInstanceOf(Response::class, $response, "Zend\Http\PhpEnvironment\Response expected. Got '".get_class($response)."' instead");
        // The response object should carry an error status 500
        $this->assertEquals(500, $response->getStatusCode(), "Response status code for non-found file should be 500. Got ".$response->getStatusCode()." instead.");
        // The response content should be a json text carrying an error message
        $this->assertContains("\"status\"    : \"error", $response->getContent());
        $this->assertContains("\"code\"      : \"-1", $response->getContent());
        $this->assertContains("\"message\"   : \"An internal error occurred. Please contact the API owner. General error message: 'TEST ERROR'\"", $response->getContent());
    }
}
