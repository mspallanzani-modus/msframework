<?php

namespace Mslib\Container;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Mslib\Controller\MsController;
use Mslib\Exception\ConfigException;
use Mslib\Exception\InitException;
use Psr\Log\LoggerInterface;
use Zend\Config\Reader\Json;

/**
 * Class Container: Platform container (services).
 *
 * This class should implement a proper service container.
 *
 * @package Mslib\Container
 */
class Container
{
//TODO implement a proper service container in here
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Initializes all required services (Logger, DB connection, ect).
     *
     * @param string $configFile The configuration file path
     * 
     * @throws ConfigException
     * @throws InitException
     */
    public function init($configFile)
    {
        // we read the config
        $this->readConfig($configFile);

        // initialize logger
        $this->setLogger($this->config, $configFile);

        // initialize db connection
        $this->setDbConnection($this->config, $configFile);
    }

    /**
     * Returns the logger instance
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Returns a valid DB Connection instance (\PDO used)
     *
     * @return \PDO
     */
    public function getDbConnection()
    {
        return $this->db;
    }

    /**
     * Returns the application configuration array
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Reads the application configuration from the given configuration file path
     *
     * @param string $configFile The configuration file path
     *
     * @throws ConfigException
     */
    protected function readConfig($configFile)
    {
        // reading the configuration
        try {
            $reader = new Json();
            $this->config = $reader->fromFile($configFile);
        } catch (RuntimeException $e) {
            throw new ConfigException(
                "It was not possible to read the given configuration file '$configFile'. " .
                "Check that it is accessible and/or it is a valid json file.");
        }
    }

    /**
     * Initializes the application logger
     *
     * @param array $data The config array containing all logger required parameters
     * @param string $configFile The configuration file name from which we read the configuration parameters
     *
     * @throws ConfigException
     * @throws InitException
     */
    protected function setLogger(array $data, $configFile)
    {
        // we check if logger is well configured
        $name = $path = $level = null;
        if (array_key_exists("logger", $data)) {
            // getting logger params from config
            $loggerParams = $data['logger'];

            // checking if name param exists
            if (!array_key_exists('name', $loggerParams)) {
                throw new ConfigException(
                    "The logger parameter 'name' is required in the given config file '$configFile'."
                );
            }
            $name = $loggerParams['name'];

            // checking if path param exists
            if (!array_key_exists('path', $loggerParams)) {
                throw new ConfigException(
                    "The logger parameter 'path' is required in the given config file '$configFile'."
                );
            }
            $path = $loggerParams['path'];

            // checking if level param exists
            if (!array_key_exists('level', $loggerParams)) {
                throw new ConfigException(
                    "The logger parameter 'level' is required in the given config file '$configFile'."
                );
            }
            $level = $loggerParams['level'];
        } else {
            throw new ConfigException("No logger parameters defined in the given config file '$configFile'.");
        }

        try {
            // we convert the level to a valid Logger constant value
            $monologLevel = Logger::toMonologLevel($level);

            // we initialize the logger
            $logger = new Logger($name);
            $logger->pushHandler(new StreamHandler($path, $monologLevel));
            $logger->pushHandler(new FirePHPHandler());

            // we save it in the container
            $this->logger = $logger;
        } catch (\Exception $e) {
            throw new InitException(
                "It was not possible to initialize the logger. Error message is: $e->getMessage()."
            );
        }
    }

    /**
     * Initializes a DB Connection with PDO
     *
     * @param array $data The config array containing all database connection required parameters
     * @param string $configFile The configuration file name from which we read the configuration parameters

     * @throws ConfigException
     * @throws InitException
     */
    protected function setDbConnection(Array $data, $configFile)
    {
        // we check if database connection is well configured
        $type = $name = $host = $user = $password = $charset = null;
        if (array_key_exists("db", $data)) {
            // getting logger params from config
            $dbParams = $data['db'];

            // checking if type param exists
            if (!array_key_exists('type', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'type' parameter is required in the given config file '$configFile'."
                );
            }
            $type = $dbParams['type'];

            // checking if name param exists
            if (!array_key_exists('name', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'name' parameter is required in the given config file '$configFile'."
                );
            }
            $name = $dbParams['name'];

            // checking if host param exists
            if (!array_key_exists('host', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'host' parameter is required in the given config file '$configFile'."
                );
            }
            $host = $dbParams['host'];

            // checking if user param exists
            if (!array_key_exists('user', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'user' parameter is required in the given config file '$configFile'."
                );
            }
            $user = $dbParams['user'];

            // checking if password param exists
            if (!array_key_exists('password', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'password' parameter is required in the given config file '$configFile'."
                );
            }
            $password = $dbParams['password'];

            // checking if charset param exists
            if (!array_key_exists('charset', $dbParams)) {
                throw new ConfigException(
                    "The database parameter 'charset' parameter is required in the given config file '$configFile'."
                );
            }
            $charset = $dbParams['charset'];
        } else {
            throw new ConfigException("No database parameters defined in the given config file '$configFile'.");
        }

        try {
            // we set the PDO options: by default, we use the fetch array mode
            $options = array(
                \PDO::ATTR_DEFAULT_FETCH_MODE   => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE              => \PDO::ERRMODE_WARNING
            );

            // we generate the db connection
            $this->db = new \PDO(
                $type . ':host=' . $host . ';dbname=' . $name . ';charset=' . $charset,
                $user,
                $password,
                $options
            );
        } catch (\Exception $e) {
            throw new InitExsception(
                "It was not possible to initialize the db connection. Error message is: $e->getMessage()."
            );
        }
    }

    /**
     * Returns a MsRepository child instance for the given Controller class
     *
     * @param string $controllerClass The namespace of the controller class
     *
     * @throws InitException
     */
    public function getRepository($controllerClass)
    {
        $repositoryClass = str_replace("Controller", "Repository", $controllerClass);
        if (class_exists($repositoryClass)) {
            return new $repositoryClass($this->db);
        } else {
            throw new InitException(
                "It was not possible to find a Repository class for the given Controller class '$controllerClass'. " .
                "Please check if you implemented a class 'Mslib\\Repository\\" . $repositoryClass . "Repository"
            );
        }
    }

    /**
     * Returns a MsController child instance for the give Controller class
     *
     * @param string $controllerClass The namespace of the controller class
     *
     * @return MsController
     *
     * @throws InitException
     */
    public function getController($controllerClass)
    {
        if (class_exists($controllerClass)) {
            return new $controllerClass($this->logger, $this->getRepository($controllerClass));
        } else {
            throw new InitException(
                "The given controller class '$controllerClass' does not exist"
            );
        }
    }
}