<?php

namespace Mslib\Repository;

use Mslib\Model\EntityInterface;
use Mslib\Model\User;

/**
 * Class UserRepository: User Repository to run all db actions on User entity
 *
 * @package Mslib\Repository
 */
class UserRepository extends MsRepository
{
    /**
     * Creates a new entity in the database for the given EntityInterface instance
     *
     * @param EntityInterface $entity The new EntityInterface instance to be added to the database
     *
     * @return bool
     */
    function create(EntityInterface $entity)
    {
        // we prepare the insert code
        $sql = "INSERT INTO user (email, firstname, lastname, password) " .
               "VALUES (:email, :firstname, :lastname, :password)";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $parameters = array(
            ':email'        => $entity->getEmail(),
            ':firstname'    => $entity->getFirstName(),
            ':lastname'     => $entity->getLastName(),
            ':password'     => $entity->getPassword());

        // we execute the query
        $query->execute($parameters);

        // we get the last inserted id
        $lastId = $this->db->lastInsertId();
        if (is_numeric($lastId) && $lastId > 0) {
            return $this->getById($lastId);
        }
        return false;
    }

    /**
     * Updates the given EntityInterface instance in the database
     *
     * @param EntityInterface $entity The EntityInterface instance to be updated in the database
     *
     * @return EntityInterface
     */
    function update(EntityInterface $entity)
    {
        // we prepare the insert code
        $set = "";
        $parameters = array(':user_id' => $entity->getId());
        if (!empty($entity->getEmail())) {
            $set .= ' email = :email,';
            $parameters[':email'] = $entity->getEmail();
        }
        if (!empty($entity->getFirstName())) {
            $set .= ' firstname = :firstname,';
            $parameters[':firstname'] = $entity->getFirstName();
        }
        if (!empty($entity->getLastName())) {
            $set .= ' lastname = :lastname,';
            $parameters[':lastname'] = $entity->getLastName();
        }
        if (!empty($entity->getPassword())) {
            $set .= ' password = :password,';
            $parameters[':password'] = $entity->getPassword();
        }
        $set = trim($set, ',');
        $sql = "UPDATE user SET $set WHERE id = :user_id";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        // we execute the query
        $result = $query->execute($parameters);

        // we get the last inserted id
        if ($result) {
            return $this->getById($entity->getId());
        }
        return $result;
    }

    /**
     * Deletes the given EntityInterface instance from the database
     *
     * @param EntityInterface $entity The EntityInterface instance to be deleted from the database
     *
     * @return bool
     */
    function delete(EntityInterface $entity)
    {
        // we prepare the delete code
        $sql = "DELETE FROM user WHERE id = :user_id";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $parameters = array(':user_id' => $entity->getId());

        // we execute it and return a boolean value: true -> deleted
        return $query->execute($parameters);
    }

    /**
     * Returns the repository name (database table name) .
     *
     * @return string
     */
    function getRepositoryName()
    {
        return "user";
    }

    /**
     * It creates and populates a new result entity with the given data array.
     *
     * @param array $data The data array with all required values to populate a result entity
     *
     * @return User
     */
    function populateEntity(array $data)
    {
        $user = new User();
        $user->populateFromArray($data);
        return $user;
    }
}