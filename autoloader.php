<?php

spl_autoload_register(function ($class) {

    $prefix = 'WaterDrinken\\';
    $baseDir = __DIR__ . '/src/';
    $lenght = strlen($prefix);
    
    if (strncmp($prefix, $class, $lenght) !== 0) {
        return;
    }

    $relativeClass = substr($class, $lenght);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }

    // spl_autoload_register( function( $name ) use ( $path ) {
    //     $filename = $path . '/' . $name . '.php';
    //     if ( file_exists( $filename ) === true ) {
    //         require $filename;
    //         return true;
    //     }
    //     return false;
    // });
});
