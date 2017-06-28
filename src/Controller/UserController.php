<?php

namespace Mslib\Controller;

use Mslib\Exception\RenderException;
use Mslib\Model\User;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\Parameters;

/**
 * Class UserController: controller in charge of executing all actions on User Model
 *
 * @package Mslib\Controller
 */
class UserController extends MsController
{
    /**
     * Returns the user information for the given GET request
     *
     * @param Request $request
     *
     * @return Response
     */
    public function show(Request $request)
    {
        // Checking the request: id is defined?
        $queryParam = $request->getUri()->getQueryAsArray();
        if (!(is_array($queryParam) && array_key_exists("id", $queryParam) && !empty($queryParam['id']))) {
            return $this->returnErrorResponse(400, "Missing 'id' parameter");
        }
        $id = $queryParam['id'];

        // We check if id is numeric
        if (!is_numeric($id)) {
            return $this->returnErrorResponse(400, "'id' parameter should be a valid integer");
        }

        // Getting the entity from the repository
        try {
            // MODEL: we get the user by id
            $user = $this->repository->getById($id);
            if (!$user instanceof User) {
                return $this->returnErrorResponse(404, "Entity not found");
            }

            // VIEW: we render the view
            $userTemplate = $this->renderEntityView($user, "User/user.json.php");

            // we return a successful response
            return $this->returnSuccessResponse($userTemplate);
        } catch (RenderException $renderException) {
            $this->logger->error(
                "An error occurred while trying to render a view. Error message is: " . $renderException->getMessage()
            );
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner. " .
                "General error message: '".$renderException->getGeneralMessage()."'"
            );
        } catch (\Exception $exception) {
            $this->logger->error("An error occurred. Error message is:" . $exception->getMessage());
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner."
            );
        }
    }

    /**
     * Creates a new user for the given POST request
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        // Checking the request: email, firstname, lastname and password are required
        // EMAIL
        $email = $request->getPost("email");
        if (empty($email)) {
            return $this->returnErrorResponse(400, "Missing 'email' parameter");
        }

        // FIRST NAME
        $firstName = $request->getPost("firstname");
        if (empty($firstName)) {
            return $this->returnErrorResponse(400, "Missing 'firstname' parameter");
        }

        // LAST NAME
        $lastName = $request->getPost("lastname");
        if (empty($lastName)) {
            return $this->returnErrorResponse(400, "Missing 'lastname' parameter");
        }

        // PASSWORD NAME
        $password = $request->getPost("password");
        if (empty($password)) {
            return $this->returnErrorResponse(400, "Missing 'password' parameter");
        }

        // Creating a new entity
        $user = $this->repository->populateEntity(array(
            'email'     => $email,
            'firstname' => $firstName,
            'lastname'  => $lastName,
            'password'  => $password
        ));

        // Getting the entity from the repository
        try {
            // MODEL: we get the user by id
            $user = $this->repository->create($user);
            if (!$user instanceof User) {
                return $this->returnErrorResponse(400, "Entity not created");
            }

            // VIEW: we render the view
            $userTemplate = $this->renderEntityView($user, "User/user.json.php");

            // we return a successful response
            return $this->returnSuccessResponse($userTemplate, 201);
        } catch (RenderException $renderException) {
            $this->logger->error(
                "An error occurred while trying to render a view. Error message is: " . $renderException->getMessage()
            );
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner. " .
                "General error message: '".$renderException->getGeneralMessage()."'"
            );
        } catch (\Exception $exception) {
            $this->logger->error("An error occurred. Error message is: " . $exception->getMessage());
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner."
            );
        }
    }

    /**
     * Edits the user for the given PUT request
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        // Getting the entity by id
        $queryParam = $request->getUri()->getQueryAsArray();
        if (!(is_array($queryParam) && array_key_exists("id", $queryParam) && !empty($queryParam['id']))) {
            return $this->returnErrorResponse(400, "Missing 'id' parameter");
        }
        $id = $queryParam['id'];

        // We check if id is numeric
        if (!is_numeric($id)) {
            return $this->returnErrorResponse(400, "'id' parameter should be a valid integer");
        }

        // Getting the entity from the repository
        try {
            // MODEL: we get the user by id
            $user = $this->repository->getById($id);
            if (!$user instanceof User) {
                return $this->returnErrorResponse(404, "Entity not found");
            }
            /** @var User $user */

            // We now get the other parameters (modifications to be done)
            $putParameters = new Parameters();
            $putParameters->fromString($request->getContent());
            $changes = $putParameters->toArray();
            $changes['id'] = $user->getId();
            $updatedUser = $this->repository->populateEntity($changes);

            // We push the changes in the DB
            $updatedUser = $this->repository->update($updatedUser);
            if (!$updatedUser instanceof User) {
                return $this->returnErrorResponse(400, "Entity not updated");
            }

            // VIEW: we render the view
            $userTemplate = $this->renderEntityView($updatedUser, "User/user.json.php");

            // we return a successful response
            return $this->returnSuccessResponse($userTemplate);
        } catch (RenderException $renderException) {
            $this->logger->error(
                "An error occurred while trying to render a view. Error message is: " . $renderException->getMessage()
            );
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner. " .
                "General error message: '".$renderException->getGeneralMessage()."'"
            );
        } catch (\Exception $exception) {
            $this->logger->error("An error occurred. Error message is: " . $exception->getMessage());
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner."
            );
        }
    }

    /**
     * Deletes the user for the given DELETE request
     *
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        // Getting the entity by id
        $queryParam = $request->getUri()->getQueryAsArray();
        if (!(is_array($queryParam) && array_key_exists("id", $queryParam) && !empty($queryParam['id']))) {
            return $this->returnErrorResponse(400, "Missing 'id' parameter");
        }
        $id = $queryParam['id'];

        // We check if id is numeric
        if (!is_numeric($id)) {
            return $this->returnErrorResponse(400, "'id' parameter should be a valid integer");
        }

        // Getting the entity from the repository
        try {
            // MODEL: we get the user by id
            $user = $this->repository->getById($id);
            if (!$user instanceof User) {
                return $this->returnErrorResponse(404, "Entity not found");
            }
            /** @var User $user */

            // We delete the user from the DB
            $deleteUser = $this->repository->delete($user);
            if (!$deleteUser) {
                return $this->returnErrorResponse(400, "Entity not deleted");
            }
            // we return a successful response
            return $this->returnSuccessResponse(array(), 200, "Entity deleted");
        } catch (\Exception $exception) {
            $this->logger->error("An error occurred. Error message is: " . $exception->getMessage());
            return $this->returnErrorResponse(
                500,
                "An internal error occurred. Please contact the API owner."
            );
        }
    }
}