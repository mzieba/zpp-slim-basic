<?php


// konfiguracja sesji
$app->container->singleton('session', function() {
    $sessionFactory = new \Aura\Session\SessionFactory;
    return $sessionFactory->newInstance($_COOKIE);
});

// stworzenie obiektu odpowiedzialnego za połączenie z BD
$app->container->singleton('pdo', function() {
    // położenie pliku konfiguracyjnego (dynamicznie tworzona nazwa)
    $fileName = 'config/db.' . (getenv('APP_ENV') ?: 'local') . '.php';

    // koniecznie sprawdzamy, czy istnieje!
    if (!file_exists($fileName)) {
        throw new Exception('no db config file!');
    }

    // wczytanie tabicy do zmiennej - unikamy przestrzeni globalnej!
    $dbConfig = include $fileName;

    $dsn = sprintf('mysql:host=%s;dbname=%s;port=%d',
            $dbConfig['host'],
            $dbConfig['name'],
            $dbConfig['port']
    );

    return new \Aura\Sql\ExtendedPdo(
        $dsn,
        $dbConfig['user'],
        $dbConfig['pass'],
        [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]
    );
});

// konfiguracja queryFactory
$app->container->singleton('query', function() use ($app) {
    $queryFactory = new \ZPP\Aura\SqlQuery\QueryFactory('mysql');
    $queryFactory->setPdo($app->pdo);
    return $queryFactory;
});
