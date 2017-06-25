<?php

require __DIR__ . '/vendor/autoload.php';


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// Create the logger
$logger = new Logger('ms_logger');
$logger->pushHandler(new StreamHandler(__DIR__.'/var/ms_app.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());

$router = new Mslib\Router\Router();
$router->init('./app/config/routing.json', $logger);
$router->resolveRequest();

