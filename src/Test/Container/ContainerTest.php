<?php

namespace Mslib\Test\Container;

use Monolog\Logger;
use Mslib\Container\Container;
use Mslib\Exception\ConfigException;
use Mslib\Exception\InitException;
use Mslib\Test\MsTestCase;

/**
 * Class ContainerTest: all Container unit tests
 *
 * @package Mslib\Test\Container
 */
class ContainerTest extends MsTestCase
{
    /**
     * Returns an array with missing parameters for Logger initialization
     *
     * @return array
     */
    public function missingLoggerParamsProvider()
    {
        return array(
            array(array("log" => array())),
            array(array("logger" => array("path" => "path", "level" => "level"))),
            array(array("logger" => array("name" => "name", "level" => "level"))),
            array(array("logger" => array("name" => "name", "path" => "path"))),
        );
    }

    /**
     * Returns an array with wrong parameters (non-existing db) for db connection initialization
     *
     * @return array
     */
    public function wrongDbParamsProvider()
    {
        return array(
            array(array("db" => array("type" => "type", "name" => "name", "host" => "host", "user" => "user", "password" => "password", "charset" => "charset"))),
        );
    }

    /**
     * Returns an array with missing parameters for db connection initialization
     *
     * @return array
     */
    public function missingDbParamsProvider()
    {
        return array(
            array(array("d" => array())),
            array(array("db" => array("name" => "name", "host" => "host", "user" => "user", "password" => "password", "charset" => "charset"))),
            array(array("db" => array("type" => "type", "host" => "host", "user" => "user", "password" => "password", "charset" => "charset"))),
            array(array("db" => array("type" => "type", "name" => "name", "user" => "user", "password" => "password", "charset" => "charset"))),
            array(array("db" => array("type" => "type", "name" => "name", "host" => "host", "password" => "password", "charset" => "charset"))),
            array(array("db" => array("type" => "type", "name" => "name", "host" => "host", "user" => "user", "charset" => "charset"))),
            array(array("db" => array("type" => "type", "name" => "name", "host" => "host", "user" => "user", "password" => "password"))),
        );
    }

    /**
     * It checks that the Container class is able to read a correct configuration file
     */
    public function testReadConfigSuccess()
    {
        // Creating the container object
        $container = new Container();

        // We read configuration variables from an existing configuration file
        $container->readConfig('./src/Test/config/config.json');

        // We check the configuration
        $config = $container->getConfig();
        $this->assertArrayHasKey("logger", $config);
        $this->assertArrayHasKey("name", $config["logger"]);
        $this->assertArrayHasKey("path", $config["logger"]);
        $this->assertArrayHasKey("level", $config["logger"]);
        $this->assertArrayHasKey("db", $config);
        $this->assertArrayHasKey("type", $config["db"]);
        $this->assertArrayHasKey("name", $config["db"]);
        $this->assertArrayHasKey("host", $config["db"]);
        $this->assertArrayHasKey("user", $config["db"]);
        $this->assertArrayHasKey("password", $config["db"]);
        $this->assertArrayHasKey("charset", $config["db"]);
        $this->assertArrayHasKey("template-folder", $config);

        // We get an existing configuration key: assert equals
        $this->assertEquals($config["template-folder"], $container->getConfigValue("template-folder"));

        // We get a non-existing configuration key: assert config exception
        $this->expectException(ConfigException::class);
        $container->getConfigValue("template-folder-no-configured");
    }

    /**
     * It checks if the expected exceptions are thrown when the configuration file does not exist
     */
    public function testReadConfigFileNotFound()
    {
        // Creating the container object
        $container = new Container();

        // We read configuration variables from an existing configuration file
        $this->expectException(ConfigException::class);
        $container->readConfig('./src/Test/config/-------config.json');
    }

    /**
     * It checks if the expected exceptions are thrown when the configuration file is in a wrong format (not JSON)
     */
    public function testReadConfigWrongJsonFormat()
    {
        // Creating the container object
        $container = new Container();

        // We read configuration variables from an existing configuration file
        $this->expectException(ConfigException::class);
        $container->readConfig('./src/Test/config/config.wrongformat.json');
    }

    /**
     * It checks if the Container class is able to correctly initialize a logger instance
     * with a valid configuration file
     */
    public function testSetLoggerSuccess()
    {
        // Creating the container object
        $configFile = './src/Test/config/config.json';
        $container = new Container();

        // We read configuration variables from an existing configuration file and we set the logger
        $container->readConfig($configFile);
        $container->setLogger($container->getConfig(), $configFile);

        // Checking if the logger has been correctly initialized
        $this->assertInstanceOf(Logger::class, $container->getLogger());
    }

    /**
     * It checks if the expected exceptions are thrown when all or some of the
     * required logger parameters are missing
     *
     * @dataProvider missingLoggerParamsProvider
     *
     * @param array $loggerParams
     */
    public function testSetLoggerMissingParameters($loggerParams)
    {
        // Creating the container object
        $configFile = './src/Test/config/config.json';
        $container = new Container();

        // The given configuration array should contain all required logger params
        $this->expectException(ConfigException::class);
        $container->setLogger($loggerParams, $configFile);
    }

    /**
     * It checks if the expected exceptions are thrown when all or some of the
     * required db connection parameters are missing
     *
     * @dataProvider missingDbParamsProvider
     *
     * @param array $dbParams
     */
    public function testSetDbConnectionMissingParameters($dbParams)
    {
        // Creating the container object
        $configFile = './src/Test/config/config.json';
        $container = new Container();

        // The given configuration array should contain all db required params
        $this->expectException(ConfigException::class);
        $container->setDbConnection($dbParams, $configFile);
    }

    /**
     * It checks if the expected exceptions are thrown when all or some of the
     * required db connection parameters are wrong
     *
     * @dataProvider wrongDbParamsProvider
     *
     * @param array $dbParams
     */
    public function testSetDbConnectionWrongParameters($dbParams)
    {
        // Creating the container object
        $configFile = './src/Test/config/config.json';
        $container = new Container();

        // We read configuration variables from an existing configuration file and we set the logger
        // The given configuration array should contain the logger index
        $this->expectException(InitException::class);
        $container->setDbConnection($dbParams, $configFile);
    }
}
