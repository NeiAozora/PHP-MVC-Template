<?php


Router::get("/", callController(IndexController::class, "index"));
Router::post("/post-test", callController(IndexController::class, "testPost"));