<?php

namespace Mslib\Test;

use Mslib\Container\Container;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

/**
 * Class MsTestCase: base class test for all project tests
 *
 * @package Mslib\Test
 */
class MsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns a Container instance with a mocked PDO connection
     *
     * @param bool $fetchUser If true, the PDO fetch action will be mocked
     * @param bool $userFound If true, the fetch action will return a valid user result set
     * @param bool $missingUserData If true, we return an array with missing data that can be used to test errors in template rendering or entity population
     *
     * @return Container
     */
    public function getContainerWitMockedPDO($fetchUser = false, $userFound = true, $missingUserData = false)
    {
        // Creating the mock object for a PDO connection
        $mockPDO = $this->createMock(\PDO::class);

        if ($fetchUser) {
            // Creating the mock object for the PDOStatement
            $mockStatement = $this->createMock(\PDOStatement::class);
            if ($userFound) {
                $mockStatement->expects($this->any())
                    ->method('fetch')
                    ->will($this->returnValue($this->getUserArray($missingUserData)));
            } else {
                $mockStatement->expects($this->any())
                    ->method('fetch')
                    ->will($this->returnValue(null));
            }

            // Injecting the statement into the PDO mock instance
            $mockPDO->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($mockStatement));
        }

        // Creating a new container instance
        $configFile = './src/Test/config/config.json';
        $container = new Container();
        $container->readConfig($configFile);
        $container->setLogger($container->getConfig(), $configFile);

        // Setting the mocked PDO connection to a new container instance
        $reflection = new \ReflectionClass($container);
        $reflection_property = $reflection->getProperty("db");
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($container, $mockPDO);

        // Returning the container instance
        return $container;
    }

    /**
     * Returns a Container instance with a mocked PDO connection that executes an update or insert action first
     * and a select action then. (used to mock create and update actions)
     *
     * @param bool $lastInsertedId If true, we mock also the lastInsertedId PDO action so that we can simulate a successful insert action
     * @param bool $execute If true, the execution of a statement will return true; null otherwise (used to simulate error in update action)
     * @param bool $userFound If true, the fetch action will return a valid user result set
     *
     * @return Container
     */
    public function getContainerWitMockedPDOExecuteQuery($lastInsertedId = true, $execute = true, $userFound = true)
    {
        // Getting user data
        $userData = $this->getUserArray();
        $userId = $userData['id'];

        // Creating the mock object for a PDO connection
        $mockPDO = $this->createMock(\PDO::class);
        $mockStatement = $this->createMock(\PDOStatement::class);
        if ($userFound) {
            $mockStatement->expects($this->any())
                ->method('fetch')
                ->will($this->returnValue($this->getUserArray()));
        }

        if ($execute) {
            $mockStatement->expects($this->any())
                ->method('execute')
                ->will($this->returnValue(true));
        } else {
            $mockStatement->expects($this->any())
                ->method('fetch')
                ->will($this->returnValue(null));
        }

        // Injecting the statement into the PDO mock instance
        $mockPDO->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockStatement));

        // Mocking lastInsertId on PDO
        if ($lastInsertedId) {
            $mockPDO->expects($this->any())
                ->method('lastInsertId')
                ->will($this->returnValue($userId));
        }

        // Creating a new container instance
        $configFile = './src/Test/config/config.json';
        $container = new Container();
        $container->readConfig($configFile);
        $container->setLogger($container->getConfig(), $configFile);

        // Setting the mocked PDO connection to a new container instance
        $reflection = new \ReflectionClass($container);
        $reflection_property = $reflection->getProperty("db");
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($container, $mockPDO);

        // Returning the container instance
        return $container;
    }

    /**
     * Returns a GET Request object for the given URI.
     *
     * @param string $uri The request uri
     *
     * @return Request
     */
    public function getGETRequestWithGetIdParam($uri)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri($uri);
        return $request;
    }

    /**
     * Returns a POST Request object for the given URI.
     *
     * @param string $uri The request uri
     * @param array $params The post parameters
     *
     * @return Request
     */
    public function getPOSTRequestWithPostParams($uri, array $params)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri($uri);
        foreach ($params as $key => $value) {
            $request->getPost()->set($key, $value);
        }
        return $request;
    }

    /**
     * Returns a PUT Request object for the given URI.
     *
     * @param string $uri The request uri
     * @param array $params The put parameters
     *
     * @return Request
     */
    public function getPUTRequestWithPutParams($uri, array $params)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_PUT);
        $request->setUri($uri);
        $content = "";
        foreach ($params as $key => $value) {
            $content = $content . $key . "=" . $value . "&";
        }
        $content = trim($content, "&");
        $request->setContent($content);
        return $request;
    }

    /**
     * Returns a DELETE Request object for the given URI.
     *
     * @param string $uri The request uri
     *
     * @return Request
     */
    public function getDELETERequestWithPutParams($uri)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setUri($uri);
        return $request;
    }

    /**
     * Returns an array representation of a User object
     *
     * @param bool $missingData If is true, we return an array with missing data that can be used to test errors in template rendering or entity population
     *
     * @return array
     */
    public function getUserArray($missingData = false)
    {
        // If missingData is true, we return an array with missing data
        if ($missingData) {
            return array(
                'id' => 1,
                'firstname' => 'test',
            );
        }
        return array(
            'id' => 1,
            'email' => 'test@testbox.dev',
            'firstname' => 'test',
            'lastname' => 'testtest',
            'password' => '12345678'
        );
    }
}