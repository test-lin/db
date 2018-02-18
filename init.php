<?php

spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'Testlin\\Db\\')) {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['\\', 'Testlin/Db/',], ['/', 'src/'], $class) . '.php';
        file_exists($filename) && include($filename);
    }
});