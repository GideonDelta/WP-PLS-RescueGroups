<?php
spl_autoload_register(function($class){
    if (0 !== strpos($class, 'RescueSync\\')) {
        return;
    }
    $relative = substr($class, strlen('RescueSync\\'));
    $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) {
        require $path;
    }
});
