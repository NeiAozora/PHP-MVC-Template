<?php


Router::get("/", callController(IndexController::class, "index"));