<?php

// Construct the URL based on the HTTPS or HTTP protocol
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

global $url;

// Include necessary files
require_once 'vendor/autoload.php';
require_once 'app/init.php';

// Initialize the app
$app = new App;

// Set a custom exception handler using Whoops
$app->setExceptionHandler(function (Throwable $e) {
    $whoops = new \Whoops\Run;
    $whoops->allowQuit(false);
    $whoops->writeToOutput(false);
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

    // Set the HTTP response code to 500 (Internal Server Error)
    http_response_code(500);
    
    // Handle and display the exception
    echo $whoops->handleException($e);
});

$app->run();
