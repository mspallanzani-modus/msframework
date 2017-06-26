<?php

namespace Mslib\Repository;

use Mslib\Model\EntityInterface;

/**
 * Class MsRepository: Abstract parent class for all framework repository
 *
 * @package Mslib\Repository
 */
abstract class MsRepository
{
    /**
     * @var \PDO Database Connection
     */
    public $db;

    /**
     * Class constructor.
     *
     * @param \PDO $db The database connection instance
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * It creates and populates a new result entity with the given data array.
     *
     * @param array $data The data array with all required values to populate a result entity
     *
     * @return EntityInterface
     */
    abstract function populateEntity(array $data);

    /**
     * Returns the repository name (database table name) .
     *
     * @return string
     */
    abstract function getRepositoryName();

    /**
     * Creates a new entity in the database for the given EntityInterface instance
     *
     * @param EntityInterface $entity The new EntityInterface instance to be added to the database
     *
     * @return EntityInterface
     */
    abstract function create(EntityInterface $entity);

    /**
     * Updates the given EntityInterface instance in the database
     *
     * @param EntityInterface $entity The EntityInterface instance to be updated in the database
     *
     * @return EntityInterface
     */
    abstract function update(EntityInterface $entity);

    /**
     * Deletes the given EntityInterface instance from the database
     *
     * @param EntityInterface $entity The EntityInterface instance to be deleted from the database
     *
     * @return bool
     */
    abstract function delete(EntityInterface $entity);

    /**
     * Returns an EntityInterface instance for the given id
     *
     * @param int $id id of the required entity
     *
     * @return EntityInterface
     */
    function getById($id)
    {
        // we prepare the select
        $sql = "SELECT * FROM " . $this->getRepositoryName() . " WHERE id = :entity_id LIMIT 1";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $parameters = array(':entity_id' => $id);

        // we execute the select
        $query->execute($parameters);

        // we fetch the result
        $data = $query->fetch();

        // we return a populated entity
        return (is_array($data) ? $this->populateEntity($data) : $data);
    }

    /**
     * Returns a fully populated EntityInterface instance for the given partially-populated EntityInterface instance
     *
     * @param EntityInterface $entity The partially-populated EntityInterface instance used to access the database
     *
     * @return EntityInterface
     */
    function get(EntityInterface $entity)
    {
        // we get the entity by id
        return $this->getById($entity->getId());
    }
}