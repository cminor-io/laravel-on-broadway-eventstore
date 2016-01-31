<?php

if (file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
    $loader = require $file;

    // Adds our own test namespace.
    $loader->add(
        'CminorIO\\LaravelOnBroadway\\EventStore\\LaravelStore',
        __DIR__
    );


    // Adds the Broadway tests namespace to include their event store tests.
    $loader->add(
        'Broadway',
        __DIR__ . '/../vendor/broadway/broadway/test'
    );

    return;
}

throw new RuntimeException('Run composer to fetch the dependencies first!');
