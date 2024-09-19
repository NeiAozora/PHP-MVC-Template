<?php

Router::getInstance()->addGlobalMiddleware(function () {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
});

Router::get("/", callController(IndexController::class, "index"));