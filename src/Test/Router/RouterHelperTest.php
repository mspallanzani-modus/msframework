<?php

namespace Mslib\Test\Router;

use Mslib\Exception\InitException;
use Mslib\Router\RouterHelper;
use Mslib\Test\MsTestCase;

/**
 * Class RouterHelperTest: all RouterHelper tests
 *
 * @package Mslib\Test\Router
 */
class RouterHelperTest extends MsTestCase
{
    /**
     * It checks if the Container class is able to correctly initialize a Repository class
     * for the given Controller class name
     */
    public function testGetRepositorySuccess()
    {
        // Getting a container instance with a mocked db connection
        $containerMockedPDO = $this->getContainerWitMockedPDO();

        // We get a repository for the given controller class name
        $userRepository = RouterHelper::getRepository("Mslib\Controller\UserController", $containerMockedPDO);
        $this->assertInstanceOf("Mslib\Repository\UserRepository", $userRepository);
    }

    /**
     * It checks if all expected exceptions are thrown when trying to get a Repository instance
     * for a non-existing Controller class name
     */
    public function testGetRepositoryNoImplementation()
    {
        // Getting a container instance with a mocked db connection
        $containerMockedPDO = $this->getContainerWitMockedPDO();

        // We should not get a valid repository for a non-existing controller class name
        $this->expectException(InitException::class);
        RouterHelper::getRepository("Mslib\Controller\NonExistingController", $containerMockedPDO);
    }

    /**
     * It checks if the Container class is able to correctly initialize a Controller class
     * for an existing controller class name
     */
    public function testGetControllerSuccess()
    {
        // Getting a container instance with a mocked db connection
        $containerMockedPDO = $this->getContainerWitMockedPDO();

        // We get a repository for the given controller class name
        $userController = RouterHelper::getController("Mslib\Controller\UserController", $containerMockedPDO);
        $this->assertInstanceOf("Mslib\Controller\UserController", $userController);
    }

    /**
     * It checks if all expected exceptions are thrown when trying to get a Controller instance
     * for a non-existing Controller class name
     */
    public function testGetControllerNoImplementation()
    {
        // Getting a container instance with a mocked db connection
        $containerMockedPDO = $this->getContainerWitMockedPDO();

        // We should not get a valid repository for a non-existing controller class name
        $this->expectException(InitException::class);
        RouterHelper::getController("Mslib\Controller\NonExistingController", $containerMockedPDO);
    }
}
