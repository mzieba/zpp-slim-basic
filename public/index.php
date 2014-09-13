<?php

// kroki, jakie podejmiemy tworząc naszą aplikację
// 0. konfiguracja slim
// 1. obsługa logowania
// 2. obsługa sesji
// 3. połączenie z BD (mysql)
// 4. widoki
// 5. prosta aplikacja (z faker)

// ustalamy, że katalogiem głównym jest katalog projektu
chdir('../');

// czy jesteśmy na localhoście
define('DEBUG', 'local' === getenv('APP_ENV'));

// dołączamy definicję autoloadera
include 'vendor/autoload.php';

// tworzymy obiekt naszej aplikacji
$app = new \Slim\Slim();

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

// przykład użycia
// pełny sposób: $monolog = $app->container->get('syslog');
DEBUG && $app->syslog->addInfo('odpalenie aplikacji', [
    'ip' => $_SERVER['REMOTE_ADDR'],
]);


// konfiguracja sesji
$app->container->singleton('session', function() {
    $sessionFactory = new \Aura\Session\SessionFactory;
    return $sessionFactory->newInstance($_COOKIE);
});

// stworzenie obiektu odpowiedzialnego za połączenie z BD
$app->container->singleton('pdo', function() {
    // położenie pliku konfiguracyjnego (dynamicznie tworzona nazwa)
    $fileName = 'config/db.' . (getenv('APP_ENV') ?: 'default') . '.php';

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

// przykład użycia fakera
$app->get('/faker-simple', function () use ($app) {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    $users = [];
    for ($i=0; $i<10; ++$i) {
        $users[] = [
            'user_name' => $generator->firstName,
            'user_surname' => $generator->lastName,
            'user_city' => $generator->city,
            'user_birthdate' => $generator->date('Y-m-d'),
        ];
    }

    var_dump($users);
});


// faker+generowanie zapytań do bazy
$app->get('/faker-insert', function () use ($app) {
    $fakerFactory = new Faker\Factory();
    $generator = $fakerFactory->create('pl_PL');
    
    // twórz zapytania dla mysql
    $queryFactory = new \Aura\SqlQuery\QueryFactory('mysql');
    // wzorzec dla zapytań insert
    $insert = $queryFactory->newInsert();
    // szczegóły zapytania
    $insert
        ->into('user')
        ->cols(['user_name', 'user_surname', 'user_city', 'user_birthdate']);
    
    // przygotuj zapytanie
    $insertStatement = $app->pdo->prepare($insert->__toString());
        
    for ($i=0; $i<10; ++$i) {
        // dane
        $user = [
            'user_name' => $generator->firstName,
            'user_surname' => $generator->lastName,
            'user_city' => $generator->city,
            'user_birthdate' => $generator->date('Y-m-d'),
        ];
        
        // wykonaj przygotowane zapytanie dla podanych danych
        $insertStatement->execute($user);
    }
    
    print 'ok';
});


// faker+generowanie zapytań do bazy
$app->get('/faker-saved', function () use ($app) {
    // twórz zapytania dla mysql
    $queryFactory = new \Aura\SqlQuery\QueryFactory('mysql');
    // wyświetl
    $select = $queryFactory->newSelect();
    $select
        ->cols(['*'])
        ->from('user');

    $rows = $app->pdo->fetchAssoc($select->__toString());
    
    var_dump($rows);
});

$app->run();


/*

$routes = [
    'hello-name' => [
        'route' => '/hello/:name',
        'controller' => 'Hello',
        'action' => 'hello',
        'defaults' => [
            'name' => 'John'
        ],
        'method' => [
            'get'
        ],
        'conditions' => [
            'name' => '\w+'
        ]
    ]
];


*/