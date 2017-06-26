<?php

namespace Mslib\Model;

/**
 * Class User: internal representation of a User
 *
 * @package Mslib\Model
 */
class User implements EntityInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $password;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

//TODO this method should be replaced by a more generic hydration mechanism
    /**
     * Populates a User instance with the give data
     *
     * @param array $data Array containing all required population values
     */
    public function populateFromArray(array $data)
    {
//TODO missing error handling on required field: if id is not defined, an error should be propragated
        // setting id
        if (array_key_exists("id", $data)) {
            $this->id = $data['id'];
        }

        // setting email
        if (array_key_exists("email", $data)) {
            $this->email = $data['email'];
        }

        // setting first name
        if (array_key_exists("firstname", $data)) {
            $this->firstName = $data['firstname'];
        }

        // setting last name
        if (array_key_exists("lastname", $data)) {
            $this->lastName = $data['lastname'];
        }

        // setting password
        if (array_key_exists("password", $data)) {
            $this->password = $data['password'];
        }
    }
}