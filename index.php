<?php

require __DIR__ . '/vendor/autoload.php';

// Initializing the app
$app = new \Mslib\App\App();
$responseInit = $app->init('./app/config/config.json', './app/config/routing.json');

// If errors occurred during the initialization process, we should get a Response object with the error information
if ($responseInit instanceof \Zend\Http\PhpEnvironment\Response) {
    $responseInit->send();
}

// Configuration was ok: we can now resolve the request
$response = $app->resolveRequest();

// Sending the response back to the client
$response->send();
