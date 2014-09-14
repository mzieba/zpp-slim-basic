<?php

// konfiguracja logów i monologa
$app->container->singleton('syslog', function() {
    $fileName = 'logs/system.log';
    
    $logger = new Monolog\Logger('syslog');
    
    // zapis do pliku
    $handler = new Monolog\Handler\RotatingFileHandler($fileName, 14);

    // dodajemy obsługę skonfigurowanego obiektu
    $logger->pushHandler($handler);
    
    // zwracamy
    return $logger;
});

// ...lub trochę krócej
$app->container->singleton('dblog', function() {
    return new Monolog\Logger('database', [
        new Monolog\Handler\RotatingFileHandler(
            'logs/database.log', 14
        )
    ]);
});