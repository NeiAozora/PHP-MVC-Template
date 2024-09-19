<?php

// Otomatis include semua controllers
foreach (glob(dirname(__DIR__ ). "/controllers/" . "*.php") as $file) {
    include $file;
}

// Otomatis include semua models
foreach (glob(dirname(__DIR__ ). "/models/" . "*.php") as $file) {
    include $file;
}
