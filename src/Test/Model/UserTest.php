<?php

namespace Mslib\Test\Model;

use Mslib\Model\User;
use Mslib\Test\MsTestCase;

/**
 * Class UserTest: all User tests
 *
 * @package Mslib\Test\Model
 */
class UserTest extends MsTestCase
{

    public function testPopulateFromArray()
    {
        // We fully populate a User instance and we check if all initial values are in the object
        $userArray = array(
            'id'        => 1,
            'email'     => 'email@email.email',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $user = new User();
        $user->populateFromArray($userArray);
        $this->assertEquals($userArray['id'], $user->getId());
        $this->assertEquals($userArray['email'], $user->getEmail());
        $this->assertEquals($userArray['firstname'], $user->getFirstName());
        $this->assertEquals($userArray['lastname'], $user->getLastName());
        $this->assertEquals($userArray['password'], $user->getPassword());

        // We partially populate a User instance and we check if all initial values are
        // in the object except for the missing ones (null expected)
        $userArray = array(
            'id'        => 1,
            'lastname'  => 'lastname',
            'password'  => 'password'
        );
        $user = new User();
        $user->populateFromArray($userArray);
        $this->assertEquals($userArray['id'], $user->getId());
        $this->assertEquals(null, $user->getEmail());
        $this->assertEquals(null, $user->getFirstName());
        $this->assertEquals($userArray['lastname'], $user->getLastName());
        $this->assertEquals($userArray['password'], $user->getPassword());
    }
}
