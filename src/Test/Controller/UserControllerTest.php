<?php

namespace Mslib\Test\Controller;

use Mslib\Controller\UserController;
use Mslib\Exception\RenderException;
use Mslib\Model\User;
use Mslib\Router\RouterHelper;
use Mslib\Test\MsTestCase;

/**
 * Class UserControllerTest: all UserController tests
 *
 * @package Mslib\Test\Controller
 */
class UserControllerTest extends MsTestCase
{
    /**
     * Returns an array containing all expected responses for the show action
     *
     * @return array
     */
    public function showActionErrorProvider()
    {
        return array(
            array("/user?id=1", true, true, false, 200, "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"\",    \"data\"      : {\"id\":\"1\",\"email\":\"test@testbox.dev\",\"firstname\":\"test\",\"lastname\":\"testtest\"}}"),
            array("/user?id=1", true, false, false, 404, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
            array("/user", true, true, false, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"),
            array("/user?id=", true, true, false, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"),
            array("/user?id=XXX", true, true, false, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"'id' parameter should be a valid integer\",    \"data\"      : []}"),
        );
    }

    /**
     * Returns an array containing all expected responses for the create action
     *
     * @return array
     */
    public function showCreateErrorProvider()
    {
        return array(
            array(
                "/user",
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                201,
                "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"\",    \"data\"      : {\"id\":\"1\",\"email\":\"test@testbox.dev\",\"firstname\":\"test\",\"lastname\":\"testtest\"}}"
            ),
            array(
                "/user",
                true,
                array(
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'email' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user",
                true,
                array(
                    'email' => 'email@email.email',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'firstname' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user",
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'lastname' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user",
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'password' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user",
                false,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not created\",    \"data\"      : []}"
            ),
        );
    }

    /**
     * Returns an array containing all expected responses for the update action
     *
     * @return array
     */
    public function showUpdateErrorProvider()
    {
        return array(
            array(
                "/user?id=1",
                true,
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                200,
                "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"\",    \"data\"      : {\"id\":\"1\",\"email\":\"test@testbox.dev\",\"firstname\":\"test\",\"lastname\":\"testtest\"}}"
            ),
            array(
                "/user",
                true,
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user?id=",
                true,
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"
            ),
            array(
                "/user?id=XXX",
                true,
                true,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"'id' parameter should be a valid integer\",    \"data\"      : []}"
            ),
            array(
                "/user?id=1",
                true,
                false,
                array(
                    'email' => 'email@email.email',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'password' => 'password'
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not updated\",    \"data\"      : []}"
            ),
            array(
                "/user?id=1",
                true,
                true,
                array(
                ),
                400,
                "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not updated\",    \"data\"      : []}"
            ),

        );
    }

    /**
     * Returns an array containing all expected responses for the delete action
     *
     * @return array
     */
    public function showDeleteErrorProvider()
    {
        return array(
            array("/user?id=1", true, true, 200, "{    \"status\"    : \"success\",    \"code\"      : \"1\",    \"message\"   : \"Entity deleted\",    \"data\"      : []}"),
            array("/user?id=1", false, true, 404, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Entity not found\",    \"data\"      : []}"),
            array("/user", true, true, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"),
            array("/user?id=", true, true, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"Missing 'id' parameter\",    \"data\"      : []}"),
            array("/user?id=XXX", true, true, 400, "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"'id' parameter should be a valid integer\",    \"data\"      : []}"),
        );
    }

    /**
     * Checks if the show action returns a correct Response object (status code and expected JSON body response).
     *
     * @param string $uri The request uri
     * @param bool $mockFetchUser If true, the PDO connection will be mocked so that it will execute the fetch action
     * @param bool $userFound If true (and if $mockFetchUser is also true), a user result set will be returned
     * @param bool $missingData If true, we return an array with missing data that can be used to test errors in template rendering or entity population
     * @param int $statusCode The expected HTTP status code
     * @param string $expectedJSONContent The expected JSON response
     *
     * @dataProvider showActionErrorProvider
     */
    public function testShow($uri, $mockFetchUser, $userFound, $missingData, $statusCode, $expectedJSONContent)
    {
        // Getting a valid user controller instance
        $mockedContainer = $this->getContainerWitMockedPDO($mockFetchUser, $userFound, $missingData);
        $userController = RouterHelper::getController('Mslib\Controller\UserController', $mockedContainer);
        $this->assertInstanceOf('Mslib\Controller\UserController', $userController);
        /** @var $userController UserController */

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->show($this->getGETRequestWithGetIdParam($uri));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedJSONContent, $response->getContent());
    }

    /**
     * It checks if a proper error is returned in case there is a render problem in the show action
     * (missing field on an entity but used in the template)
     */
    public function testShowRenderError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDO(true);

        // We mock the repository class so that it returns a mock of User with some missing fields
        // (we want to throw a rendering error)
        $mockUser = $this->createMock('Mslib\Model\User');
        $mockUser->expects($this->any())
            ->method('getId')
            ->will($this->throwException(new \Exception()));

        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('getById')
            ->will($this->returnValue($mockUser));

        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->show($this->getGETRequestWithGetIdParam("/user?id=1"));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner. General error message: 'Render Error'\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * It checks if a proper error is returned in case there is a general problem in the show action
     * (e.g. a database access throw an exception)
     */
    public function testShowInternalError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDO(true);

        // We mock the repository class so that it throws an exception (general internal error.. like a db access error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('getById')
            ->will($this->throwException(new \Exception()));

        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->show($this->getGETRequestWithGetIdParam("/user?id=1"));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner.\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * Checks if the create action returns a correct Response object
     * (status code and expected JSON body response).
     *
     * @param string $uri The request uri
     * @param bool $lastInsertedId If true, we mock also the lastInsertedId PDO action so that we can simulate a successful insert action
     * @param array $postParams The post parameters
     * @param int $statusCode The expected HTTP status code
     * @param string $expectedJSONContent The expected JSON response
     *
     * @dataProvider showCreateErrorProvider
     */
    public function testCreate($uri, $lastInsertedId, array $postParams, $statusCode, $expectedJSONContent)
    {
        // Getting a valid user controller instance
        $mockedContainer = $this->getContainerWitMockedPDOExecuteQuery($lastInsertedId);
        $userController = RouterHelper::getController('Mslib\Controller\UserController', $mockedContainer);
        $this->assertInstanceOf('Mslib\Controller\UserController', $userController);
        /** @var $userController UserController */

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->create($this->getPOSTRequestWithPostParams($uri, $postParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedJSONContent, $response->getContent());
    }

    /**
     * It checks if a proper error is returned in case there is a render problem in the create action
     * (missing field on an entity but used in the template)
     */
    public function testCreateRenderError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDOExecuteQuery();

        // We mock the repository class so that it throws a rendering exception
        // (we want to throw a rendering error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('create')
            ->will($this->throwException(new RenderException()));

        // The method populateEntity has to return a User entity that will be used to generate the sql code
        $postParams = array(
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $requestUser = new User();
        $requestUser->populateFromArray($postParams);
        $mockRepository->expects($this->any())
            ->method('populateEntity')
            ->will($this->returnValue($requestUser));

        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->create($this->getPOSTRequestWithPostParams("/user", $postParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner. General error message: 'Render Error'\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * It checks if a proper error is returned in case there is a general problem
     * (e.g. a database access throw an exception)
     */
    public function testCreateInternalError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDOExecuteQuery();

        // We mock the repository class so that it throws a rendering exception
        // (we want to throw a rendering error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('create')
            ->will($this->throwException(new \Exception()));

        // The method populateEntity has to return a User entity that will be used to generate the sql code
        $postParams = array(
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $requestUser = new User();
        $requestUser->populateFromArray($postParams);
        $mockRepository->expects($this->any())
            ->method('populateEntity')
            ->will($this->returnValue($requestUser));

        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->create($this->getPOSTRequestWithPostParams("/user", $postParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner.\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * Checks if the update action returns a correct Response object
     * (status code and expected JSON body response).
     *
     * @param string $uri The request uri
     * @param bool $lastInsertedId If true, we mock also the lastInsertedId PDO action so that we can simulate a successful insert action
     * @param bool $execute If true, the execution of a statement will return true; null otherwise (used to simulate error in update action)
     * @param array $putParams The put parameters
     * @param int $statusCode The expected HTTP status code
     * @param string $expectedJSONContent The expected JSON response
     *
     * @dataProvider showUpdateErrorProvider
     */
    public function testUpdate($uri, $lastInsertedId, $execute, array $putParams, $statusCode, $expectedJSONContent)
    {
        // Getting a valid user controller instance
        $mockedContainer = $this->getContainerWitMockedPDOExecuteQuery($lastInsertedId, $execute);
        $userController = RouterHelper::getController('Mslib\Controller\UserController', $mockedContainer);
        $this->assertInstanceOf('Mslib\Controller\UserController', $userController);
        /** @var $userController UserController */

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->edit($this->getPUTRequestWithPutParams($uri, $putParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedJSONContent, $response->getContent());
    }

    /**
     * It checks if a proper error is returned in case there is a render problem in the update action
     * (missing field on an entity but used in the template)
     */
    public function testUpdateRenderError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDOExecuteQuery();

        // We mock the repository class so that it throws a rendering exception
        // (we want to throw a rendering error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('update')
            ->will($this->throwException(new RenderException()));

        // The method populateEntity has to return a User entity that will be used to generate the sql code
        $putParams = array(
            'id'        => 1,
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $requestUser = new User();
        $requestUser->populateFromArray($putParams);
        $mockRepository->expects($this->any())
            ->method('populateEntity')
            ->will($this->returnValue($requestUser));

        $mockRepository->expects($this->any())
            ->method('getById')
            ->will($this->returnValue($requestUser));

        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->edit($this->getPUTRequestWithPutParams("/user?id=1", $putParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner. General error message: 'Render Error'\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * It checks if a proper error is returned in case there is a general problem in the update action
     * (e.g. a database access throw an exception)
     */
    public function testUpdateInternalError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDOExecuteQuery();

        // We mock the repository class so that it throws a rendering exception
        // (we want to throw a rendering error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('update')
            ->will($this->throwException(new \Exception()));

        // The method populateEntity has to return a User entity that will be used to generate the sql code
        $postParams = array(
            'id'        => 1,
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $requestUser = new User();
        $requestUser->populateFromArray($postParams);
        $mockRepository->expects($this->any())
            ->method('populateEntity')
            ->will($this->returnValue($requestUser));

        $mockRepository->expects($this->any())
            ->method('getById')
            ->will($this->returnValue($requestUser));


        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->edit($this->getPUTRequestWithPutParams("/user?id=1", $postParams));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner.\",    \"data\"      : []}",
            $response->getContent()
        );
    }

    /**
     * Checks if the update action returns a correct Response object
     * (status code and expected JSON body response).
     *
     * @param string $uri The request uri
     * @param bool $userFound If true (and if $mockFetchUser is also true), a user result set will be returned
     * @param bool $execute If true, the execution of a statement will return true; null otherwise (used to simulate error in update action)
     * @param int $statusCode The expected HTTP status code
     * @param string $expectedJSONContent The expected JSON response
     *
     * @dataProvider showDeleteErrorProvider
     */
    public function testDelete($uri, $userFound, $execute, $statusCode, $expectedJSONContent)
    {
        // Getting a valid user controller instance
        $mockedContainer = $this->getContainerWitMockedPDOExecuteQuery(true, $execute, $userFound);
        $userController = RouterHelper::getController('Mslib\Controller\UserController', $mockedContainer);
        $this->assertInstanceOf('Mslib\Controller\UserController', $userController);
        /** @var $userController UserController */

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->delete($this->getDELETERequestWithPutParams($uri));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($expectedJSONContent, $response->getContent());
    }

    /**
     * It checks if a proper error is returned in case there is a general problem in the delete action
     * (e.g. a database access throw an exception)
     */
    public function testDeleteInternalError()
    {
        // We get a Container instance with a mocked PDO connection
        $container = $this->getContainerWitMockedPDOExecuteQuery();

        // We mock the repository class so that it throws a rendering exception
        // (we want to throw a rendering error)
        $mockRepository = $this->createMock('Mslib\Repository\UserRepository');
        $mockRepository->expects($this->any())
            ->method('delete')
            ->will($this->throwException(new \Exception()));

        // The method populateEntity has to return a User entity that will be used to generate the sql code
        $userParams = array(
            'id'        => 1,
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $requestUser = new User();
        $requestUser->populateFromArray($userParams);
        $mockRepository->expects($this->any())
            ->method('populateEntity')
            ->will($this->returnValue($requestUser));

        $mockRepository->expects($this->any())
            ->method('getById')
            ->will($this->returnValue($requestUser));


        // Getting a valid user controller instance
        $userController = new UserController($container->getLogger(), $mockRepository, $container);

        // Calling the show method: we should get a valid Response object with the expected status code and json content
        $response = $userController->delete($this->getDELETERequestWithPutParams("/user?id=1"));
        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            "{    \"status\"    : \"error\",    \"code\"      : \"-1\",    \"message\"   : \"An internal error occurred. Please contact the API owner.\",    \"data\"      : []}",
            $response->getContent()
        );
    }

}
